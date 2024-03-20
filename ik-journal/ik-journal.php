<?php
/*
Plugin Name: Journal Online
Plugin URI: https://inforket.com/
Description: Journal online con frases y preguntas para completar diariamente
Version: 1.1.1
Author: Gabriel Caroprese
Author URI: https://inforket.com/
*/ 


// I create a DB table to manage quotes for journaling
register_activation_hook( __FILE__, 'ik_journal_quotes_db_tables' );
function ik_journal_quotes_db_tables() {
	global $wpdb;
	
	// 
	$charset_collate = $wpdb->get_charset_collate();
	$table_name1 = $wpdb->prefix . 'ik_journal_quotes';
	$table_name2 = $wpdb->prefix . 'ik_journal_replies';
	$table_name3 = $wpdb->prefix . 'ik_journal_records';
	$sql = "CREATE TABLE $table_name1 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		quote_n mediumint(9) NOT NULL,
		quote longtext NOT NULL,
		author tinytext NOT NULL,
	    lang varchar(5) NOT NULL,
	    showed_date date DEFAULT '0000-00-00' NOT NULL,
		recommendation longtext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $table_name2 (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
	    month int(2) NOT NULL,
	    year int(4) NOT NULL,
	    quote_ns longtext NOT NULL,
	    thankfull_q longtext NOT NULL,
	    todayGreat_q longtext NOT NULL,
        daily_affirmation longtext NOT NULL,
        important_task longtext NOT NULL,
        amazing_happenings longtext NOT NULL,
        today_better longtext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $table_name3 (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
	    records_dates longtext NOT NULL,
	    year int(4) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

//I create a menu to add information to journales
function ik_journal_menu_info() {
  register_nav_menu('ik_journal_menu',__( 'Journals Menu' ));
}
add_action( 'init', 'ik_journal_menu_info' );


// I add vars I'm going to use in the plugin
function ik_journal_vars_add( $vars ){
    $vars[] = "search";
    $vars[] = "jday";
    $vars[] = "jmonth";
    $vars[] = "jyear";
    return $vars;
}
add_filter( 'query_vars', 'ik_journal_vars_add' );


// Function to check for available quotes to show
function ik_journal_quotes_available($typeQ, $langQuote, $get_date_quote){
    global $wpdb;
    $language = strtoupper(sanitize_text_field($langQuote));

    if ($typeQ == 0){
        $dateData = "AND showed_date = '".$get_date_quote."'";
    } else if ($typeQ == 1){
        $dateData = "AND showed_date = '0000-00-00'";
    } else {
        $dateData = "";
        //I clean all other dates
        ik_journal_clear_quotes_shown();
    }
    
    $getQuoteQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_quotes WHERE lang = '".$language."' ".$dateData." ORDER BY quote_n ASC LIMIT 1";
    $getQuote = $wpdb->get_results($getQuoteQuery);
    
    
    //I check that there's some quote available
    if (isset($getQuote[0]->quote_n)){

        //I check if no quote with the same date was shown

        $shownQuoteQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_quotes WHERE lang = '".$language."' AND showed_date = '".$get_date_quote."' ORDER BY quote_n ASC LIMIT 1";
        $shownQuote = $wpdb->get_results($shownQuoteQuery);

        if (isset($shownQuote[0]->quote_n)){
            return $shownQuote;
        } else {
            //I add date to quote about to be shown
            global $wpdb;
            $tableUpdateDate = $wpdb->prefix.'ik_journal_quotes';
            $where = [ 'quote_n' => $getQuote[0]->quote_n ];
                
            $data_date  = array (
                            'showed_date'=>$get_date_quote,
                    );
            $rowResult = $wpdb->update($tableUpdateDate,  $data_date , $where);
            return $getQuote;
        } 
    } else {
        return NULL;
    }
}

//Function to clear dates of quotes shown
function ik_journal_clear_quotes_shown(){
    //Please I check if any quote exist
    global $wpdb;
    $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."ik_journal_quotes SET showed_date = '0000-00-00'"));
}


//Function to get data recorded on journal
function ik_journal_get_datos($journalDate, $dataJournal){
    if (isset($dataJournal)){
        global $wpdb;
        $monthJournal = idate('m', $journalDate);
        $yearJournal = idate('Y', $journalDate);
        $dataRow = $dataJournal;
        
        $getJournalQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_replies WHERE user_id = '".get_current_user_id()."' AND month = ".$monthJournal." AND year = ".$yearJournal;
        $getJournal = $wpdb->get_results($getJournalQuery);
        
        if (isset($getJournal[0]->$dataRow)){
            return maybe_unserialize($getJournal[0]->$dataRow);
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
}



//Function to assign and show quotes from different authors
function ik_journal_show_quote($lang, $jdate_quote){

    $language = strtoupper(sanitize_text_field($lang));
    
    // I check if value is not null 
    if (isset(ik_journal_quotes_available(0, $language, $jdate_quote)[0]->quote)){
        //already shown today or before
        $getQuote = ik_journal_quotes_available(0, $language, $jdate_quote);
    } else if (isset(ik_journal_quotes_available(1, $language, $jdate_quote)[0]->quote)){
        //not showed yet
        $getQuote = ik_journal_quotes_available(1, $language, $jdate_quote);
    } else if (isset(ik_journal_quotes_available(2, $language, $jdate_quote)[0]->quote)){
        //repeat quote already shown before
        $getQuote = ik_journal_quotes_available(2, $language, $jdate_quote);
    } else {
        return;
    }
    
    // I show the quote
    $quoteToShow = '<div class="quote-full-today">
                        <span class="today-quote">“'.$getQuote[0]->quote.'”</span>
                        <span class="today-quote-author">'.$getQuote[0]->author.'</span>
                    </div>';
    return $quoteToShow;
}

//Function to assign and show quotes from different authors from a specific past journal
function ik_journal_show_quote_by_journaldate($lang, $date_journal){

    $language = strtoupper(sanitize_text_field($lang));
    $dayID = date('d', strtotime($date_journal));
    $get_quote_data = ik_journal_get_datos(strtotime($date_journal), 'quote_ns');

    if (isset($get_quote_data[intval($dayID)]['quote'])){
        global $wpdb;
        $getQuoteQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_quotes WHERE quote_n = ".$get_quote_data[intval($dayID)]['quote']." AND lang = '".$language."'  ORDER BY quote_n ASC LIMIT 1";
        $getQuote = $wpdb->get_results($getQuoteQuery);
        
        
        //I check that there's some quote available
        if (isset($getQuote[0]->id)){
        
            // I show the quote
            $quoteToShow = '<div class="quote-full-today">
                                <span class="today-quote">“'.$getQuote[0]->quote.'”</span>
                                <span class="today-quote-author">'.$getQuote[0]->author.'</span>
                            </div>';
            return $quoteToShow;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


//Function to show recommendation of the day
function ik_journal_show_quote_recommendation($lang, $jdate_quote){

    $language = strtoupper(sanitize_text_field($lang));
    
    $getRecommendation = "";
    
    // I check if value is not null 
    if (isset(ik_journal_quotes_available(0, $language, $jdate_quote)[0]->recommendation)){
        $recomendation = ik_journal_quotes_available(0, $language, $jdate_quote)[0]->recommendation;
        if ($recomendation != ''){
            $getRecommendation = '<div class="ik-journal-recommendation">'.$recomendation.'</div>';    
        }
    }
    
    return $getRecommendation;
}


//Function to get quote showed in specific recent day
function ik_journal_get_recent_quote($dateShownQuote, $langShown){
    
    $dateToConvert = sanitize_text_field($dateShownQuote);
    $dateToCheck = date("Y-m-d", $dateToConvert);
    $language_reference = strtoupper($langShown);
    
    global $wpdb;
    $getQuoteDateQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_quotes WHERE showed_date = '".$dateToCheck."' AND lang = '".$language_reference."'";
    $getQuoteDate = $wpdb->get_results($getQuoteDateQuery);
    
    if (isset($getQuoteDate[0]->quote_n)){
        return $getQuoteDate[0]->quote_n;
    }
    
}

//Function to get last journal date
function ik_journal_getPrevNextJournal($key, $hash = array(), $direction){
    

    if (strtotime(date( 'Y-m-d')) == $key && $direction == "nuevos"){
        // Es hoy, así que no hay siguiente
        return NULL;
        
    } else {
        /* 
            Si es hoy posiblemente pueda haber nada salvado y entonces no va a poder 
            comparar. Entonces lo que hago es que pueda ver el último registro,
            si es que hay uno.
        */
        if (strtotime(date( 'Y-m-d')) == $key && $direction == "viejos"  ){
    
            //Si la última fecha es hoy
           if (array_key_last($hash) == strtotime(date( 'Y-m-d')) ){
                $keys = array_keys($hash);
                $found_index = array_search(array_key_last($hash), $keys);
                 $indexN = $found_index - 1;

                if (isset($keys[$indexN])){
                    return $keys[$indexN];
                } else {
                    return NULL;
                }
           } else {
               return array_key_last($hash);
           }
            
            /* 
                Si es ayer tengo que hacer lo mismo con el sentido de navegar hacia
                el siguiente.
            */
        } else if (strtotime(date('Y-m-d',strtotime("-1 day"))) == $key && $direction == "nuevos"){
    
               return 0;
               
        }else {
            /* 
                Si no es hoy simplemente va a mostrar siguiente o anterior registro
                dependiendo el sentido que se navegue
            */
            
            $keys = array_keys($hash);
            $found_index = array_search($key, $keys);
            if ($found_index === false || $found_index === -1){
                return NULL;
            } else {
                if ($direction == 'nuevos'){
                    $indexN = $found_index + 1;
                } else {
                    $indexN = $found_index - 1;
                }
                if (isset($keys[$indexN])){
                    return $keys[$indexN];
                } else {
                    return NULL;
                }
            }
        }
    }
}


//Function to search journals matching a keyword 
function ik_journal_search_results($textToSearch){
    $keywordJournal = sanitize_text_field($textToSearch);    
    
    global $wpdb;
    $JournalKeywordQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_replies WHERE user_id = '".get_current_user_id()."' AND (thankfull_q LIKE '%".$keywordJournal."%' OR todayGreat_q LIKE '%".$keywordJournal."%' OR daily_affirmation LIKE '%".$keywordJournal."%' OR important_task LIKE '%".$keywordJournal."%' OR amazing_happenings LIKE '%".$keywordJournal."%' OR today_better LIKE '%".$keywordJournal."%')";
    $JournalAllQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_replies WHERE user_id = '".get_current_user_id()."'";
    //Chequeo si se seleccionó la palabra clave "journals" para buscar todo
    if ($keywordJournal == 'journals'){
        $query = $JournalAllQuery;
    } else {
        $query = $JournalKeywordQuery;
    }
    $getResultsKeyword = $wpdb->get_results($query);
    
    if (isset($getResultsKeyword[0]->quote_ns)){
        return $getResultsKeyword;
    } else{
        return NULL;
    }
}

//It shows a message for results not found
function ik_journal_result_not_found($current_page){
    return "<p>".__( 'No se encontraron journals que coincidan con tu criterio de búsqueda.', 'ik-journal' )."</p>
    <div class='ik-journal-return'> ".sprintf( __( '<a href="%s">Volver al journal actual.</a>', 'ik-journal'), $current_page)."</div>";
}

//Function to find words in a array
function ik_journal_findkeyword_inarray($arrayToSearch, $keywordinArray){
    if ($arrayToSearch == NULL || $keywordinArray == NULL){
        return false;
    } else {
        $seachonNoCaps = strtolower($arrayToSearch);
        $seachKeywordonNoCaps = strtolower($keywordinArray);
        if (strpos($seachonNoCaps, $seachKeywordonNoCaps) != false){
            return true;
        } else {
            return false;
        }
    }
}


//Function to check if there's an older or newer journal to see and retrieve its date number
function ik_journal_check_older_newer($navigation, $actualDate){

    //Veo el year actual
    $yearJournal = idate('Y', $actualDate);
    
    //Primero chequeo el registro del año actual
    $recordJournal = ik_journal_search_old_new_records($yearJournal);
    
    //Chequeo y defino los años de registro donde buscar
    if ($recordJournal != NULL){

        if ($navigation == 'viejos'){
            $journalAnterior = ik_journal_getPrevNextJournal($actualDate, $recordJournal, 'viejos');
            if ($journalAnterior != NULL){
                
                return ik_journal_nextprev_link($journalAnterior, 'Ver Anterior');
                
            } else {
                $otroYear = $yearJournal - 1;
                
                //Busco en otro año
                $yearAnterior = ik_journal_search_old_new_records($otroYear);
                
                if ($yearAnterior != NULL){
                    
                    //Consigo el último registro de ese array 
                    return ik_journal_nextprev_link(array_key_last($yearAnterior), 'Ver Anterior');
                    
                } else {

                    return NULL;
                }
            }
                        
        
        } else if ($navigation == 'nuevos'){
            $journalSiguiente = ik_journal_getPrevNextJournal($actualDate, $recordJournal, 'nuevos');

            if ($journalSiguiente != NULL){
                if ($journalSiguiente == 0){
                    
                    // si el siguiente es el día de hoy
                    return ik_journal_nextprev_link(0, 'Ver Siguiente');
                
                } else {
                    return ik_journal_nextprev_link($journalSiguiente, 'Ver Siguiente');
                }
            } else {

                $otroYear = $yearJournal + 1;
                
                //Busco en otro año
                $yearSiguiente = ik_journal_search_old_new_records($otroYear);
                if ($yearSiguiente != NULL){
                    
                    // Consigo el primer registro de ese array
                    return ik_journal_nextprev_link(array_key_first($yearSiguiente), 'Ver Siguiente');
                    
                } else {

                    return ik_journal_nextprev_link(0, 'Ver Siguiente');
                }
            }
        } else {
            return NULL;
        }
    }
}

//function para devolver link de siguiente o anterior journal
function ik_journal_nextprev_link($dateIndex, $AntoSig){

        //chequeo la URL donde estoy
        $pageobject_id = get_queried_object_id();
        $current_pageURL = get_permalink( $pageobject_id );
        
    if ($dateIndex == 0){
        //Va al día de hoy
        $link_nav = '<a href="'.$current_pageURL.'">'.__( $AntoSig, 'ik-journal' ).'</a>';
    } else{
        
        $link_nav = '<a href="'.$current_pageURL.'?journal='.$dateIndex.'">'.__( $AntoSig, 'ik-journal' ).'</a>';

    }
    return $link_nav;
}


// Ajax to upload data about journal
add_action( 'wp_ajax_ik_journal_ajax_upload_field', 'ik_journal_ajax_upload_field');
function ik_journal_ajax_upload_field() {

    if(isset($_POST['journal_field']) && isset($_POST['data_field']) && isset($_POST['date_journal']) && isset($_POST['language']) && get_current_user_id() != 0){
            $journal_field = sanitize_text_field($_POST['journal_field']);
            $data_field = sanitize_textarea_field($_POST['data_field']);
            $data_field = str_replace("\'", "'", $data_field);
            $data_field = str_replace('\"', '"', $data_field);
            $date_journal = sanitize_text_field($_POST['date_journal']);
            $language = sanitize_text_field($_POST['language']);
            $quote_ns = sanitize_text_field($_POST['quote']);


            $dayJournal = idate('d', $date_journal);
            $monthJournal = idate('m', $date_journal);
            $yearJournal = idate('Y', $date_journal);
            
            if ($journal_field == 'gracias_field'){
                $journal_row = 'thankfull_q';
            } else if ($journal_field == 'todaygreat_field'){
                $journal_row = 'todayGreat_q';
            } else if ($journal_field == 'affirmation_field'){
                $journal_row = 'daily_affirmation';
            } else if ($journal_field == 'important_field'){
                $journal_row = 'important_task';
            } else if ($journal_field == 'amazing_field'){
                $journal_row = 'amazing_happenings';
            } else if ($journal_field == 'better_field'){
                $journal_row = 'today_better';
            } else {
                $journal_row = NULL;
            }
            

        // I check if the field already has info about this day or other day of the month
        if (ik_journal_get_datos($date_journal, 'user_id') != NULL){
            
            // I check if there's already data inserted for this row in particular
            if (ik_journal_get_datos($date_journal, $journal_row) != NULL){
                
                $arrayJournalData = ik_journal_get_datos($date_journal, $journal_row);
                
            } 
            
            $arrayJournalData[$dayJournal] = array(
                                                    'datetime' => current_datetime('timestamp', wp_timezone()),
                                                    'record' => $data_field
                                            );       
                    
            //I get the array
            global $wpdb;
            $tableData = $wpdb->prefix.'ik_journal_replies';

            $journalDataSerialized = maybe_serialize($arrayJournalData);
            global $wpdb;
            $where = [ 
                    'user_id'   => get_current_user_id(),
                    'month'     => $monthJournal,
                    'year'      => $yearJournal,
            ];
            
            $journal_to_record  = array (
                                    $journal_row    => $journalDataSerialized,
                                    );
            $wpdb->update($tableData, $journal_to_record, $where);
            
            
            
            // I save the quote shown
            
            // I check if there's already data inserted for this row in particular
            if (ik_journal_get_datos($date_journal, 'quote_ns') != NULL){
                
                $arrayJournalDate = ik_journal_get_datos($date_journal, 'quote_ns');
                
            } 
            
            $arrayJournalDate[$dayJournal] = array(
                'quote' => $quote_ns,
                );
            $journalDateSerialized = maybe_serialize($arrayJournalDate);
            
            global $wpdb;
            $tableDataQuote = $wpdb->prefix.'ik_journal_replies';
            $whereQuote = [ 
                    'user_id'   => get_current_user_id(),
                    'month'     => $monthJournal,
                    'year'      => $yearJournal,
                    ];
            $quote_to_record  = array (
                                    'quote_ns' => $journalDateSerialized
                                );
            $wpdb->update($tableDataQuote, $quote_to_record, $whereQuote);
                       
            /*  
                I insert record history to the records table
                First I check if there's already a record for this year
            */
            ik_journal_records_register(date( 'Y-m-d'));
            
            
        } else {
            $arrayJournalData[$dayJournal] = array(
                'datetime' => current_datetime('timestamp', wp_timezone()),
                'record' => $data_field,
                );
            $journalDataSerialized = maybe_serialize($arrayJournalData);
            
            global $wpdb;
            $tableDataQuote = $wpdb->prefix.'ik_journal_replies';
            $journal_to_record  = array (
                            'user_id'=> get_current_user_id(),
                            'month'=> $monthJournal,
                            'year'=> $yearJournal,
                            $journal_row => $journalDataSerialized
                    );
            $wpdb->insert($tableDataQuote, $journal_to_record);
            
            
            // I save the quote shown
            
            $arrayJournalDate[$dayJournal] = array(
                'quote' => $quote_ns,
                );
            $journalDateSerialized = maybe_serialize($arrayJournalDate);
            
            global $wpdb;
            $tableDataQuote = $wpdb->prefix.'ik_journal_replies';
            $whereQuote = [ 
                    'user_id'   => get_current_user_id(),
                    'month'     => $monthJournal,
                    'year'      => $yearJournal,
                    ];
            $quote_to_record  = array (
                                    'quote_ns' => $journalDateSerialized
                                );
            $wpdb->update($tableDataQuote, $quote_to_record, $whereQuote);
            
            /*  
                I insert record history to the records table
                First I check if there's already a record for this year
            */
            ik_journal_records_register(date( 'Y-m-d'));
            
        }
        
        echo json_encode( date('d.m.Y H:i', current_datetime('timestamp', wp_timezone())->getTimestamp()));
        wp_die();
        
    } else {
        wp_send_json_error();
    }
}

// Ajax to send report about quotes
add_action( 'wp_ajax_ik_journal_ajax_report_quote', 'ik_journal_ajax_report_quote');
function ik_journal_ajax_report_quote(){
    if(isset($_POST['id_reporte_quote']) && isset($_POST['lang_reporte_quote']) && get_current_user_id() != 0){
        $id_reporte_quote = sanitize_text_field($_POST['id_reporte_quote']);
        $lang_reporte_quote = sanitize_text_field($_POST['lang_reporte_quote']);
        $comentarios_report = sanitize_text_field($_POST['comentarios_report']);
        

        // valido si el idioma es más de dos caracteres y id de fecha de frase/quote no es número
        if (strlen($lang_reporte_quote) == 2 || is_int($id_reporte_quote)){

            //I send email with report about quote
            $to = get_option('admin_email');
            $subject = 'Reporte sobre Frases en Journal';
            $body = '<p>Recibiste un reporte en 
            '.get_option('blogname').' ('.get_site_url().') sobre la frase: 
            '.ik_journal_show_quote($lang_reporte_quote, date( 'Y-m-d', $id_reporte_quote)).'</p>';

            $user = get_user_by( 'id',  get_current_user_id() );
            $body .= '<p>El usuario que hizo el reporte fue ' . $user->user_login.'</p>';
            $body .= '<p>Comentario: '.$comentarios_report.'</p>';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            wp_mail( $to, $subject, $body, $headers );
            echo json_encode( __( 'Reporte enviado. ¡Gracias!', 'ik-journal' ));
            wp_die();
        } else {
            wp_send_json_error();
        }
        
    } else {

        wp_send_json_error();
    }
}



// Function to search for old or new records in the DB
function ik_journal_search_old_new_records($yearRecord){

    global $wpdb;
    $getJournalRecordsQuery = "SELECT * FROM ".$wpdb->prefix."ik_journal_records WHERE user_id = '".get_current_user_id()."' AND year = ".$yearRecord;
    $getJournalRecords = $wpdb->get_results($getJournalRecordsQuery);
    
    if (isset($getJournalRecords[0]->records_dates)){
        return maybe_unserialize($getJournalRecords[0]->records_dates);
    } else {
        return NULL;
    }
}


// function to register records dates in records table
function ik_journal_records_register($dateRecord){
    $startDateJournal = strtotime($dateRecord);
    $yearJournal = idate('Y', $startDateJournal);

    if (ik_journal_search_old_new_records(date( 'Y')) == NULL){
        $record_date_journal[$startDateJournal] = "1";
        $record_date_journalSerialized = maybe_serialize($record_date_journal);
        
        global $wpdb;
        $tableRecords = $wpdb->prefix.'ik_journal_records';
        $date_record  = array (
                        'user_id'=> get_current_user_id(),
                        'records_dates'=> $record_date_journalSerialized,
                        'year'=> $yearJournal
                );
        $wpdb->insert($tableRecords, $date_record);
    
        
    } else {
    
        //If data doesn't exist about this day and month, I add it
        if (!isset(ik_journal_search_old_new_records(date( 'Y'))[$startDateJournal])){
            $record_date_journal = ik_journal_search_old_new_records(date( 'Y'));
        
            $record_date_journal[$startDateJournal] = "1";
            $record_date_journalSerialized = maybe_serialize($record_date_journal);
            
            global $wpdb;
            $tableRecords = $wpdb->prefix.'ik_journal_records';
            $date_record  = array (
                            'records_dates'=> $record_date_journalSerialized,
                    );
                    
            $whereRecords = [ 
                    'user_id'   => get_current_user_id(),
                    'year'      => $yearJournal,
                    ];
                    
            $wpdb->update($tableRecords, $date_record, $whereRecords);
        }
    }
}

//Function to add script to upload info to journal
function ik_journal_script_add($dateToJournal, $languageQuote) {
    $scriptJournal = '<script> 
        jQuery(document).on("click", ".ik-boton-guardar button", function(){
            ik_upload_journal_data(this);
        });
        jQuery(document).on("blur", ".ik_fields_journal textarea", function(){
            if (!jQuery(this).val().length == 0){
                var buttonRequest = jQuery(this).parent().find(".ik-boton-guardar button");
                ik_upload_journal_data(buttonRequest);
            }
        });
        function ik_upload_journal_data(reference_button){
            var journal_field = jQuery(reference_button).attr("related");
            var date_journal = '.$dateToJournal.';
            var data_field = jQuery("#"+journal_field).val();
            var language = "'.$languageQuote.'";
            var journalDataID = jQuery("#"+journal_field);    
            
            jQuery(reference_button).prop("disabled", true);

            journalDataID.parent().find(".ik-boton-guardar button .ik-journal-loading").attr("style","display: inline-block;");

            var data = {
                action: "ik_journal_ajax_upload_field",
                "post_type": "post",
                "journal_field": journal_field,
                "date_journal": date_journal,
                "data_field": data_field,
                "language": language,
                "quote": '.ik_journal_get_recent_quote($dateToJournal, $languageQuote).',
            };  

            // The variable ajax_url should be the URL of the admin-ajax.php file
            jQuery.post( "' . admin_url('admin-ajax.php') . '", data, function(response) {
                    setTimeout(function(){
                        journalDataID.parent().find(".ik-boton-guardar .ik-journal-loading").attr("style", "display: none");
        				journalDataID.parent().find(".ik-journal-uploaded").attr("style","display: block");
                        jQuery(reference_button).prop("disabled", false);
        				journalDataID.parent().find(".ik-journal-uploaded").fadeOut(4000); 
        				journalDataID.parent().find(".ik-datetime-updated-journal").attr("style", "display: none");
        				journalDataID.parent().find(".ik-datetime-updated-journal").text("'.__( 'Última Actualización: ', 'ik-journal' ).'"+response);
        				journalDataID.parent().find(".ik-datetime-updated-journal").fadeIn(1500);
                    }, 700);

            }, "json");
        }
        jQuery( document ).ajaxError(function() {
            location.reload();
        });
        jQuery(document).on("click", ".report-quote-button", function(){
            jQuery("#ik-reportar-popup").attr("style", "display: block! important;");
        });
        jQuery(document).on("click", ".ik-closetab", function(){
            jQuery("#ik-reportar-popup").attr("style", "display: none! important;");
        });
        jQuery(document).on("click", "#ik-reportar-submit", function(){
            var id_reporte_quote = jQuery("#id_reporte_quote").val();
            var lang_reporte_quote = jQuery("#lang_reporte_quote").val();
            var comentarios_report = jQuery("#comentarios_report").val();
            
            jQuery("#ik-reportar-submit").prop("disabled", true);

            var data = {
                action: "ik_journal_ajax_report_quote",
                "post_type": "post",
                "lang_reporte_quote": lang_reporte_quote,
                "id_reporte_quote": id_reporte_quote,
                "comentarios_report": comentarios_report,
            };  

            // The variable ajax_url should be the URL of the admin-ajax.php file
            jQuery.post( "' . admin_url('admin-ajax.php') . '", data, function(response) {
                jQuery("#report-quote-sent").text(response);
                setInterval(function(){
                    jQuery("#ik-reportar-popup").fadeOut(500);
                }, 3000);
            }, "json");
        });
    </script>';
    return $scriptJournal;
    
}


// Shortcode to make the journaling
function ik_journal_show($atts, $content = null ) {
    ob_start();
    $journalShow = 1;
    include('templates/journal.php');  
    return ob_get_clean();
}
add_shortcode('ik_journal_show', 'ik_journal_show');

//Load language files
function ik_journal_textdomain_init() {
    load_plugin_textdomain( 'ik-journal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'ik_journal_textdomain_init' );


?>