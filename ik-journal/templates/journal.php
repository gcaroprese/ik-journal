<?php
/*
    
    Journal Template
    Author: S. Gabriel Caroprese
    Inforket - 31/12/2020

*/
if (isset($journalShow)){
    
    //Chequeo web URL
    $pageobject_id = get_queried_object_id();
    $current_pageURL = get_permalink( $pageobject_id );
        
    //Chequeo si hubo alguien se encuentra buscando algo
    if (isset($_POST['search_journal']) || isset($_POST['search_journal_option'])){
        
         if (isset($_POST['search_journal']) && isset($_POST['search_journal_option'])){   
            $searchedJournal = sanitize_text_field($_POST['search_journal']);
            $searchedDateOption = intval($_POST['search_journal_option']);
            
             if ($searchedDateOption == 1 && isset($_POST['search_journal_day']) && isset($_POST['search_journal_month']) && isset($_POST['search_journal_year'])){
                $searchedDateDay = intval($_POST['search_journal_day']);
                $searchedDateMonth = intval($_POST['search_journal_month']);
                $searchedDateYear = intval($_POST['search_journal_year']);
   		        
   		        
   		        if (($searchedDateDay != NULL && $searchedDateDay != 0) || ($searchedDateMonth != NULL && $searchedDateMonth != 0) || ($searchedDateYear != NULL && $searchedDateYear != 0)){
   		            $searchJournalAction = true;
   		            $searchedJournalMore = "";
       		        //I create a variable to create the URL
       		        $dateSearchGet = "";
       		        if ($searchedDateDay != NULL && $searchedDateDay != 0){
                        if ($searchedDateDay < 10){
       		                $dateSearchGet .= '&jday=0'.$searchedDateDay;                            
                        } else {
       		                $dateSearchGet .= '&jday='.$searchedDateDay;                            
                        }
       		        }
       		        if ($searchedDateMonth != NULL && $searchedDateMonth != 0){
                        if ($searchedDateDay < 10){
       		                $dateSearchGet .= '&jmonth=0'.$searchedDateMonth;                         
                        } else {
       		                $dateSearchGet .= '&jmonth='.$searchedDateMonth;                   
                        }
       		        }
       		        if ($searchedDateYear != NULL && $searchedDateYear != 0){
       		            $dateSearchGet .= '&jyear='.$searchedDateYear;
       		        }
   		        }
                    echo '<script>
					window.location.replace("'.$current_pageURL.'/?search='.$searchedJournal.$dateSearchGet.'");
				</script>';
             } else if (!empty($searchedJournal) && $searchedDateOption == 0){
                $searchJournalAction = true;
                $searchedJournalMore = "";
		        echo '<script>
					window.location.replace("'.$current_pageURL.'/?search='.$searchedJournal.'");
				</script>';
             } else {
                $searchJournalAction = false;
             }
                            
         } 


    } else if (isset($_GET['search'])){
        $searchedJournal = sanitize_text_field($_GET['search']);  


        //Muestra los resultados
        $searchJournalAction = true;
        $searchedJournalEmpty = '';
        
        // Defino el valor de los campos de search de fechas
        if (isset($_GET['jday']) || isset($_GET['jmonth']) || isset($_GET['jyear'])){
            $searchedJournalMore = 'checked="checked"';
            $search_journal_option = 1;
            $requiredSearchField = '';   

            //Si no se ingresaron keywords y es solo fecha agrego la palabra clave "journals" para que busque igual
            if ($searchedJournal == '' || empty($searchedJournal || $searchedJournal == NULL)){
                $searchedJournal = 'journals';
            }      

            if (isset($_GET['jday'])){
                $searchedJournalDay = intval($_GET['jday']);
                if ($searchedJournalDay < 10){
                    $searchedJournalDay = '0'.$searchedJournalDay;                            
                }
            } else {
                $searchedJournalDay = $searchedJournalEmpty;
            }
            if (isset($_GET['jmonth'])){
                $searchedJournalMonth = intval($_GET['jmonth']);
                if ($searchedJournalMonth < 10){
                    $searchedJournalMonth = '0'.$searchedJournalMonth;                            
                }
            } else {
                $searchedJournalMonth = $searchedJournalEmpty;
            }
            if (isset($_GET['jyear'])){
                $searchedJournalYear = intval($_GET['jyear']);
            } else {
                $searchedJournalYear = $searchedJournalEmpty;
            }
        } else {
            $searchedJournalMore = "";
            $requiredSearchField = 'required';        
            $search_journal_option = 0; 
            $searchedJournalDay = $searchedJournalEmpty;
            $searchedJournalMonth = $searchedJournalEmpty;
            $searchedJournalYear = $searchedJournalEmpty;
        }
        
        include('journal-search.php');
    } else {
        $searchJournalAction = false;
        $requiredSearchField = 'required';
    }
    
    if ($searchJournalAction == false) {
        
        //Si no hubieron busquedas
        $searchedJournalEmpty = '';
        $search_journal_option = 0;
        $searchedJournal = $searchedJournalEmpty;
        $searchedJournalDay = $searchedJournalEmpty;
        $searchedJournalMonth = $searchedJournalEmpty;
        $searchedJournalYear = $searchedJournalEmpty;
        $searchedJournalMore = 'class="ik-more-uncheck"';
        
    
        //Defino la fecha de hoy
        $TodayDate = strtotime(date( 'd.m.Y'));

        if (isset($_GET['journal']) && !empty($_GET['journal'])){
            $getdateThisJournal = sanitize_text_field($_GET['journal']);
            
            $dayJournal = idate('d', $getdateThisJournal);
            $monthJournal = idate('m', $getdateThisJournal);
            $yearJournal = idate('Y', $getdateThisJournal);
            if (checkdate ( $monthJournal, $dayJournal, $yearJournal ) == true){
                $dateThisJournal = intval($_GET['journal']);
            } else {
                $dateThisJournal = $TodayDate;
            }

        } else {
            $dateThisJournal = $TodayDate;
            $dayJournal = idate('d', $dateThisJournal);
            $monthJournal = idate('m', $dateThisJournal);
        }

        if ($dateThisJournal == $TodayDate){
            if (function_exists('pll_current_language')) {
                if (ik_journal_show_quote_by_journaldate(pll_current_language(), date( 'Y-m-d', $dateThisJournal)) !== false){
                    $journalQuoteGetFunction = "ik_journal_show_quote_by_journaldate";
                } else {
                    $journalQuoteGetFunction = "ik_journal_show_quote";
                }
            } else {
                $journalQuoteGetFunction = "ik_journal_show_quote";
            }
        } else {
            $journalQuoteGetFunction = "ik_journal_show_quote_by_journaldate";
        }
        
        //I check language active and I add quote depending on the current day
        if (function_exists('pll_current_language')) {
            if(pll_current_language() == 'en' ){
                $languageActive = 'en';
                $quoteOfTheDay = $journalQuoteGetFunction('en', date( 'Y-m-d', $dateThisJournal));
                $recommendationOfTheDay = ik_journal_show_quote_recommendation('en', date( 'Y-m-d', $dateThisJournal));
            } else if(pll_current_language() == 'it' ){
                $languageActive = 'it';
                $quoteOfTheDay = $journalQuoteGetFunction('it', date( 'Y-m-d', $dateThisJournal));
                $recommendationOfTheDay = ik_journal_show_quote_recommendation('it', date( 'Y-m-d', $dateThisJournal));
            } else if(pll_current_language() == 'pr' ){
                $languageActive = 'pr';
                $quoteOfTheDay = $journalQuoteGetFunction('pr', date( 'Y-m-d', $dateThisJournal));
                $recommendationOfTheDay = ik_journal_show_quote_recommendation('pr', date( 'Y-m-d', $dateThisJournal));
            } else {
                $languageActive = 'es';
                $quoteOfTheDay = $journalQuoteGetFunction('es', date( 'Y-m-d', $dateThisJournal));
                $recommendationOfTheDay = ik_journal_show_quote_recommendation('es', date( 'Y-m-d', $dateThisJournal));
            }
        } else {
            $languageActive = 'es';
            $quoteOfTheDay = $journalQuoteGetFunction('es', date( 'Y-m-d', $dateThisJournal));
            $recommendationOfTheDay = ik_journal_show_quote_recommendation('es', date( 'Y-m-d', $dateThisJournal));
        }
        
        // I create a variable for incomplete fields.
        $datoIncompleto = '';
        
        // I set text to show with the update date
        $texto_actualizacion = __( 'Última Actualización: ', 'ik-journal' );


        if (ik_journal_get_datos($dateThisJournal, 'user_id') != NULL){
            // I show data recorded for every field
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'thankfull_q')[$dayJournal]['record'])){
                $graciasDato = ik_journal_get_datos($dateThisJournal, 'thankfull_q')[$dayJournal]['record'];
                $graciasupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'thankfull_q')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $graciasDato = $datoIncompleto;
                $graciasupdateDate = $datoIncompleto;
            }
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'todayGreat_q')[$dayJournal]['record'])){
                $todaygreatDato = ik_journal_get_datos($dateThisJournal, 'todayGreat_q')[$dayJournal]['record'];
                $todaygreatupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'todayGreat_q')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $todaygreatDato = $datoIncompleto;
                $todaygreatupdateDate = $datoIncompleto;
            }
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'daily_affirmation')[$dayJournal]['record'])){
                $affirmationsDato = ik_journal_get_datos($dateThisJournal, 'daily_affirmation')[$dayJournal]['record'];
                $affirmationsupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'daily_affirmation')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $affirmationsDato = $datoIncompleto;
                $affirmationsupdateDate = $datoIncompleto;
            }
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'important_task')[$dayJournal]['record'])){
                $importantDato = ik_journal_get_datos($dateThisJournal, 'important_task')[$dayJournal]['record'];
                $importantupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'important_task')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $importantDato = $datoIncompleto;
                $importantupdateDate = $datoIncompleto;
            }
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'amazing_happenings')[$dayJournal]['record'])){
                $amazingDato = ik_journal_get_datos($dateThisJournal, 'amazing_happenings')[$dayJournal]['record'];
                $amazingupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'amazing_happenings')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $amazingDato = $datoIncompleto;
                $amazingupdateDate = $datoIncompleto;
            }
            
            if (isset(ik_journal_get_datos($dateThisJournal, 'today_better')[$dayJournal]['record'])){
                $betterDato = ik_journal_get_datos($dateThisJournal, 'today_better')[$dayJournal]['record'];
                $betterupdateDate = $texto_actualizacion.date('d.m.Y H:i', ik_journal_get_datos($dateThisJournal, 'today_better')[$dayJournal]['datetime']->getTimestamp());
            } else {
                $betterDato = $datoIncompleto;
                $betterupdateDate = $datoIncompleto;
            }
           

        } else{
            
            //Show data empty for empty fields
            $graciasDato = $datoIncompleto;
            $todaygreatDato = $datoIncompleto;
            $affirmationsDato = $datoIncompleto;
            $importantDato = $datoIncompleto;
            $amazingDato = $datoIncompleto;
            $betterDato = $datoIncompleto;
            $graciasupdateDate = $datoIncompleto;
            $todaygreatupdateDate = $datoIncompleto;
            $affirmationsupdateDate = $datoIncompleto;
            $importantupdateDate = $datoIncompleto;
            $amazingupdateDate = $datoIncompleto;
            $betterupdateDate = $datoIncompleto;
        }
        
    ?>
    
        <section id="ik-journal">
            <span class="journal-fecha-hoy"><?php _e( 'Fecha: ', 'ik-journal' ); echo date('d.m.Y', $dateThisJournal); ?></span>
            <div class="ik-leer-quote"><?php _e( 'Para leer y pensar:', 'ik-journal' ); ?>
                <?php echo $quoteOfTheDay;  ?>
                <span class="report-quote-button"><i class="fas fa-exclamation-triangle"></i><?php _e( 'Reportar Frase', 'ik-journal' ); ?></span>
                <div id="ik-reportar-popup">
                    <div class="ik-closetab"><i class="fas fa-times"></i></div>
                    <span class="popup-reportar-titulo"><?php _e( 'Reportar Frase', 'ik-journal' ); ?></span>
                    <textarea placeholder="<?php _e( 'Comentarios', 'ik-journal' ); ?>" name="comentarios_report" id="comentarios_report"></textarea>
                    <button id="ik-reportar-submit"><?php _e( 'Enviar', 'ik-journal' ); ?></button>
                    <input type="hidden" id="id_reporte_quote" name="id_reporte_quote" value="<?php echo $dateThisJournal; ?>" />
                    <input type="hidden" id="lang_reporte_quote" name="lang_reporte_quote" value="<?php echo $languageActive; ?>" />
                    <span id="report-quote-sent"></span>
                </div>
            </div>
            <?php
            if ($dateThisJournal == $TodayDate){
                echo $recommendationOfTheDay;
            ?>
            
                <div id="ik_gracias_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Estoy agradecido/a por...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="gracias_field" name='gracias_field' placeholder="<?php _e( 'Escribí tu agradecimiento', 'ik-journal' ); ?>"><?php echo $graciasDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $graciasupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="gracias_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>
                <div id="ik_todaygreat_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Este día sería perfecto si...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="todaygreat_field" name='todaygreat_field' placeholder="<?php _e( 'Escribí algo que haría este día algo memorable', 'ik-journal' ); ?>"><?php echo $todaygreatDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $todaygreatupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="todaygreat_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>                
                <div id="ik_affirmation_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Afirmaciones Diarias. Yo soy...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="affirmation_field" name='affirmation_field' placeholder="<?php _e( 'Escribí tus afirmaciones', 'ik-journal' ); ?>"><?php echo $affirmationsDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $affirmationsupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="affirmation_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>
                <div id="ik_important_field_box" class="ik_fields_journal">
                    <label><?php _e( 'La tarea que más me molesta, pero que tengo que hacer para tener un día exitoso es...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="important_field" name='important_field' placeholder="<?php _e( 'Escribí la tarea más importante que tenés para hacer hoy', 'ik-journal' ); ?>"><?php echo $importantDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $importantupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="important_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>            
                <div id="ik_amazing_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Cosas buenas que pasaron hoy...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="amazing_field" name='amazing_field' placeholder="<?php _e( 'Contá algo sobre las cosas buenas y sorprendentes que pasaron hoy', 'ik-journal' ); ?>"><?php echo $amazingDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $amazingupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="amazing_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>
                <div id="ik_better_field_box" class="ik_fields_journal">
                    <label><?php _e( '¿Cómo podría haber hecho para que el día fuera mejor?', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="better_field" name='better_field' placeholder="<?php _e( 'Escribí ideas de cosas que podrías haber hecho que pasaran para mejorar el día', 'ik-journal' ); ?>"><?php echo $betterDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $betterupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="better_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>
                
            <?php
            } else {
            ?>
                <div id="ik_gracias_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Estoy agradecido/a por...', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $graciasDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $graciasupdateDate; ?></span>
                </div>
                <div id="ik_todaygreat_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Este día sería perfecto si...', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $todaygreatDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $todaygreatupdateDate; ?></span>
                </div>                
                <div id="ik_affirmation_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Afirmaciones Diarias. Yo soy...', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $affirmationsDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $affirmationsupdateDate; ?></span>
                </div>
                <div id="ik_important_field_box" class="ik_fields_journal">
                    <label><?php _e( 'La tarea que más me molesta, pero que tengo que hacer para tener un día exitoso es...', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $importantDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $importantupdateDate; ?></span>
                </div>          
                <?php 
                
                // Si hacen pocas horas desde acabado esta date permito editar algunos campos

                $currentTime = intval(date('H'));
                $dateTimeJournal = date('d-m-Y', $dateThisJournal);
                $JournalOfYesterday = date('d-m-Y', strtotime('yesterday'));
                if ($currentTime <= 14 && $dateTimeJournal == $JournalOfYesterday) {
                    ?>
                    
                <div id="ik_amazing_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Cosas buenas que pasaron hoy...', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="amazing_field" name='amazing_field' placeholder="<?php _e( 'Contá algo sobre las cosas buenas y sorprendentes que pasaron hoy', 'ik-journal' ); ?>"><?php echo $amazingDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $amazingupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="amazing_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>
                <div id="ik_better_field_box" class="ik_fields_journal">
                    <label><?php _e( '¿Cómo podría haber hecho para que el día fuera mejor?', 'ik-journal' ); ?></label>   
                    <textarea autocomplete="off" id="better_field" name='better_field' placeholder="<?php _e( 'Escribí ideas de cosas que podrías haber hecho que pasaran para mejorar el día', 'ik-journal' ); ?>"><?php echo $betterDato; ?></textarea>
                    <span class="ik-datetime-updated-journal"><?php echo $betterupdateDate; ?></span>
                    <div class="ik-boton-guardar">
                        <button related="better_field"><span><span class="ik-journal-loading"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/img/loading.gif" alt="loading journal" /></span><?php _e( 'Guardar', 'ik-journal' ); ?></span></button>
                        <span class="ik-journal-uploaded" style="display: none"><?php _e( 'Guardado', 'ik-journal' ); ?></span>
                    </div>
                </div>

                <?php
                } else {
                ?>
                <div id="ik_amazing_field_box" class="ik_fields_journal">
                    <label><?php _e( 'Cosas buenas que pasaron hoy...', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $amazingDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $amazingupdateDate; ?></span>
                </div>
                <div id="ik_better_field_box" class="ik_fields_journal">
                    <label><?php _e( '¿Cómo podría haber hecho para que el día fuera mejor?', 'ik-journal' ); ?></label>   
                    <div class="ik-content-journal-field"><?php echo $betterDato; ?></div>
                    <span class="ik-datetime-updated-journal"><?php echo $betterupdateDate; ?></span>
                </div>
                <?php 
                }
            }
            ?>
            <div class="ik-journal-navegar">
                
                
                <?php
                
                    // I add script to upload journal to DB
                    echo ik_journal_script_add($dateThisJournal, $languageActive); 

                
                    //Agrego para navegar para journals anteriores o siguientes
                    $JournalAnteriorLink = ik_journal_check_older_newer('viejos', $dateThisJournal);
                    $JournalSiguienteLink = ik_journal_check_older_newer('nuevos', $dateThisJournal);
                    
                    if ($JournalAnteriorLink != NULL){
                        $navAnterior = true;
                        echo '<span class="ik-journal-anterior"><i class="fas fa-arrow-alt-circle-left"></i>'.ik_journal_check_older_newer('viejos', $dateThisJournal).'</span>';
                    }
                    if ($dateThisJournal != $TodayDate){
                        if ($JournalSiguienteLink != NULL){
                            $navsiguiente = true;
                            echo '<span class="ik-journal-siguiente">'.ik_journal_check_older_newer('nuevos', $dateThisJournal).'<i class="fas fa-arrow-alt-circle-right"></i></span>';
                        }
                    }
                    ?>
            </div>
            <?php
            
                    if ($dateThisJournal != $TodayDate){
                        if ($JournalSiguienteLink != NULL){
                            // Botón para volver al journal de hoy si estoy en journal distante
                            echo '<div class="ik-journal-return"><a href="'.$current_pageURL.'">'.__( 'Volver al journal actual', 'ik-journal').'</a></div>';
                            
                            //Script para quitarlo si la siguiente página es la actual
                            echo '<script>
                            if (jQuery(".ik-journal-siguiente a").attr("href") == "'.$current_pageURL.'"){
                                jQuery(".ik-journal-return").remove();
                            }
                            </script>';
                        }
                    }
            
            
            
                }
                // Agrego buscador                         
                if (isset($navAnterior) || isset($navsiguiente) || isset($searchedJournal)){
                
                ?>
                <div id="ik-journal-search">
                <form action="" method="post" id="ik-journal-searcher">
                <input <?php echo $requiredSearchField; ?> type="text" name="search_journal" id="ik-search_journal-keyword" placeholder="<?php _e( 'Buscá en tus journals...', 'ik-journal' ); ?>" value="<?php echo $searchedJournal; ?>" />
                <input type="submit" id="ik-journal-searcher-submit" value="<?php _e( 'Buscar', 'ik-journal' ); ?>" />
                <div class="ik-journal-search-more-opciones">   
                    <label>
                        <input type="checkbox" name="search_journal_mas" id="search_journal_mas" <?php echo $searchedJournalMore; ?>><span><?php _e( 'Más Opciones', 'ik-journal' ); ?></span>
                    </label>
                </div>
                <div id="ik-journal-search-more" style="display: none;" action="0">
                    <input type="number" name="search_journal_day" id="ik_search_journal_day" placeholder="<?php _e( 'Día', 'ik-journal' ); ?>" value="<?php echo $searchedJournalDay; ?>" />
                    <input type="number" name="search_journal_month" id="ik_search_journal_month" placeholder="<?php _e( 'Mes', 'ik-journal' ); ?>" value="<?php echo $searchedJournalMonth; ?>" />
                    <input type="number" name="search_journal_year" id="ik_search_journal_year" placeholder="<?php _e( 'Año', 'ik-journal' ); ?>" value="<?php echo $searchedJournalYear; ?>" />
                </div>
                <input type="hidden" name="search_journal_option" id="search_journal_option" value='<?php echo $search_journal_option; ?>' />
                </form>
                </div>
                <div class="ik_journal_menu">
                    <?php
                    if ( has_nav_menu( 'ik_journal_menu' ) ) {
                        wp_nav_menu( array( 
                            'theme_location' => 'ik_journal_menu', 
                            'container_class' => 'ik_journal_menuinfo' ) );
                    }
                    ?>
                </div>
                <?php
                }
                ?>
        
        
        </section>
        <link href="<?php echo plugin_dir_url( __DIR__ ); ?>css/fontawesome/css/all.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@200;500;800&display=swap" rel="stylesheet">
        <script>
        if (jQuery('#search_journal_mas').attr("checked") == "checked"){
            jQuery('#ik-journal-search-more').attr('style', 'display: block');
        }
        function checkDate(dateFormat){
            if (dateFormat == 'd'){
                var dayValor = jQuery('#ik_search_journal_day').val();
                if (dayValor == ''){
                    return true;
                } else {
                    var dayValor = parseInt(jQuery('#ik_search_journal_day').val());
                    if (dayValor > 0 && dayValor < 32){
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if (dateFormat == 'm'){
                var monthValor = jQuery('#ik_search_journal_month').val();
                if (monthValor == ''){
                    return true;
                } else {
                    var monthValor = parseInt(jQuery('#ik_search_journal_month').val());
                    if (monthValor > 0 && monthValor < 13){
                        return true;
                    } else {
                        return false;
                    }
                }
            } else if (dateFormat == 'Y'){
                var yearValor = jQuery('#ik_search_journal_year').val();
                if (yearValor == ''){
                    return true;
                } else {
                    var yearValor = jQuery('#ik_search_journal_year').val();
                    var text = /^[0-9]+$/;
                    if (yearValor != 0) {
                        if (!text.test(yearValor)) {
                            return false;
                        } else if (yearValor.length != 4) {
                            if (yearValor.length == 2) {
                                jQuery('#ik_search_journal_year').val('20'+yearValor);
                                return true;
                            } else {
                                alert(yearValor.length);
                                return false;
                            }
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
        jQuery('#ik-journal-searcher').submit(function() {
            if (checkDate('d') == true && checkDate('m') == true && checkDate('Y') == true){
                return true;
            } else {
                jQuery('#ik-journal-search-more input').attr('style', 'border: 2px solid red');  
                return false;
            }
        });
        jQuery(document).ready(function () {
            jQuery('#search_journal_mas').change(function () {
                if (this.checked){
                    jQuery('#ik-journal-search-more').fadeIn('slow');  
                    jQuery('#ik-journal-searcher-submit').appendTo('#ik-journal-searcher');  
                    jQuery('#search_journal_option').val(1);  
                    jQuery('#ik-search_journal-keyword').attr('required', false);  
                    jQuery(this).removeClass('ik-more-uncheck');  
                }
                else {
                    jQuery('#ik-journal-search-more').fadeOut('slow');  
                    jQuery('#ik-journal-searcher-submit').insertAfter('#ik-search_journal-keyword');  
                    jQuery('#search_journal_option').val(0);  
                    jQuery('#ik-search_journal-keyword').attr('required', true);  
                    jQuery(this).addClass('ik-more-uncheck');  
                }
            });
        });
        setInterval(function(){
            jQuery('#ik-journal textarea').attr('maxlength', '420');
        }, 1000);
        </script>
        <style>
            #ik-journal {
                margin: 0 auto;
                max-width: 90%;
                display: block;
            }
            #ik-journal, #ik-journal-search-results{
                font-family: 'Roboto Slab', serif;
                font-weight: 200;
            }
            .journal-fecha-hoy{
                text-align: center;
                margin: 20px auto 40px;
                display: block;
                background: #f1f1f1;
                max-width: 215px;
                border-radius: 20px;
                padding: 2px 7px;
            }
            .ik-boton-guardar button .ik-journal-loading, #ik-reportar-popup{
                display: none;
            }
            .ik-boton-guardar button .ik-journal-loading img {
                width: 20px;
                margin-right: 7px;
                position: relative;
                top: -1px;
            }
            .ik-leer-quote {
                font-size: 20px;
                padding: 4%;
                background: #f1f1f1;
                margin: 40px auto;
                border-radius: 12px;
                max-width: 800px;
                display: block;
            }
            .today-quote{
                font-weight: 500;
            }
            .quote-full-today{
                margin: 20px 7px;
                line-height: 1.5;
            }
            .today-quote-author{
                display: block;
                font-weight: 800;
            }
            .today-quote-author:before {
                content: '—';
                margin-right: 5px;
            }
            .report-quote-button{
                cursor: pointer;
                font-size: 14px;
                float: right;
                font-style: italic;
                position: relative;
                top: -7px;
                margin: 0px 3px;
            }
            #ik-reportar-popup {
                position: absolute;
                background: #444;
                width: 90%;
                max-width: 400px;
                padding: 20px;
                transform: translate(-50%, -50%);
                left: 50%;
                margin: 0 auto;
                border-radius: 15px;
            }
            #ik-reportar-popup span{
                color: #fff;
                font-weight: 500;
            }
            #ik-reportar-submit{
                background: #fff;
                border: 0;
                padding: 4px 12px;
                color: #444;
                text-transform: uppercase;
                font-size: 18px;
            }
            .ik-closetab{
                float: right;
                cursor: pointer;
                color: #fff;
            }
            #report-quote-sent{
                display: block;
                font-size: 17px;
            }
            #ik-reportar-popup .popup-reportar-titulo, #ik-reportar-popup textarea, #ik-reportar-popup input{
                display: block;
                margin: 12px 0;
            }
            #ik-reportar-popup textarea {
                background: #757575;
                width: 100%! important;
                min-width: 200px! important;
            }
            #ik-reportar-popup textarea::placeholder, #ik-reportar-popup textarea::-webkit-input-placeholder, #ik-reportar-popup textarea::placeholder, #ik-reportar-popup textarea:-moz-placeholder, #ik-reportar-popup textarea::-moz-placeholder, #ik-reportar-popup textarea:-ms-input-placeholder {
            color: #444;  
            }
            .ik-journal-recommendation{
                padding: 10px 2%;
                margin: 0 auto;
                display: block;
            }
            .ik_fields_journal, .ik-journal-navegar {
                max-width: 600px;
                margin: 45px auto;
            }
            #ik-journal label {
                margin-bottom: 5px;
                font-size: 20px;
                display: block;
            }
            #ik-journal textarea {
                line-height: 1.4;
                border: 1px solid #eee;
                padding: 5px 10px;
                box-shadow: none;
                text-shadow: none;
                min-width: 265px;
                max-width: 600px;
                height: 120px;
                width: 100%;
                font-size: 20px;
            }   
            .ik-datetime-updated-journal{
                display: block;
                margin: 8px 0px;
                font-size: 13px;
            }
            #ik-journal ::-moz-selection {
              background: #000;
              text-shadow: none;
            }
            #ik-journal ::selection {
              background: #000;
              text-shadow: none;
            }
            .ik-journal-uploaded{
                font-size: 13px;
                margin: 2px 2px;
                position: absolute;
            }
            .ik_fields_journal button{
                min-width: 135px;
                text-align: center;
            }
            .ik-journal-navegar span{
                padding: 5px;
            }
            .ik-journal-siguiente{
                float: right;
            }
            .ik-journal-anterior{
                float: left;
            }
            .ik-journal-anterior i, .ik-journal-siguiente i, .report-quote-button i {
                margin: 0 3px;
                position: relative;
                top: 3px;
            }
            .ik-journal-anterior i, .ik-journal-siguiente i{
                 font-size: 23px;               
            }
            .report-quote-button i {
                font-size: 17px;
            }
            .ik-content-journal-field {
                background: #f1f1f1;
                min-height: 100px;
                padding: 20px;
            }
            .ik_fields_journal, .ik-journal-navegar{
                max-width: 586px;
            }
            .ik-journal-navegar {
                display: flow-root;
                margin: 20px auto;
                padding: 0 2%;
                width: 100%;
                text-align: center;
            }
            #ik-journal-search{
                display: block;
                margin: 50px auto;
                text-align: center;
            }
            #search_journal_mas:before{ margin-left: -16px; }
            #ik-journal-search-results{
                max-width: 700px;
                margin: 1em auto 3em;
            }
            #ik-journal-search-results .ik_journal_texto_found{
                font-weight: 300;
            }
            .ik-journal-search-more-opciones span{
                position: relative;
                top: 5px;
            }
            #ik-journal-searcher input {
                text-align: center;
                max-width: 250px;
            }
            .ik-journal-search-more-opciones input{
                position: relative;
                top: 4px;
                margin-right: 7px;
                width: 20px;
                height: 20px;
            }
            #ik-journal-search-more input{
                width: 90px;
                height: 45px;
            }
            .ik-journal-search-more-opciones input[type=checkbox]:after {
                left: 4px;
                top: 0px;
            }
            #ik_search_results_journals p{
                text-align: center;
            }
            #ik-journal-search-more input::-webkit-outer-spin-button,
            #ik-journal-search-more input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }
            
            #ik-journal-search-more input[type=number] {
              -moz-appearance: textfield;
            }
            #ik-journal-search-more, .ik-journal-search-more-opciones span{
                margin: 10px auto;
                font-size: 16px;
            }
            #ik-journal-search-results{
                padding: 0 5%;
            }
            #ik-journal-search-results .ik_journal_listed_search {
                padding: 20px;
                border: 1px solid;
                margin: 20px 0;
            }
            .ik-journal-return a{
                background: rgb(59, 59, 59);
                color: #fff;
                padding: 8px 15px;
                margin: 55px auto;
                display: table;
                text-decoration: none;
                text-transform: capitalize;
            }
            .ik_journal_menuinfo ul {
                background: #f1f1f1;
                width: 100%;
                max-width: 250px;
                margin: 5em auto 0;
                display: block;
                padding: 5px 15px;
                text-align: center;
            }
            .ik_journal_menuinfo ul li{
                display: block;
            }
            .ik_journal_menuinfo ul li a{
                text-transform: uppercase;
                font-family: "Roboto Slab", serif;
                margin-left: 3px;
            }
            .ik_journal_menuinfo ul li {
                display: block;
                padding: 10px 0;
                border-top: 1px solid #ccc;
            }
            .ik_journal_menuinfo ul li:first-child {
                border-top: 0px solid #ccc;
            }
            @media (max-width: 767px){
                #ik-journal textarea {
                    min-height: 155px;
                    height: auto;
                }
                .ik-leer-quote {
                    display: inline-block! important;
                }
                #ik-journal-search label{
                    width: 90%;
                    text-align: center;
                    margin: 0 auto;
                }
                #ik-journal-searcher input[type=text], #ik-journal-searcher input[type=submit] {
                    text-align: center;
                    width: 72%;
                }
            }
        </style>
        
<?php
    } else {
        echo '<section id="ik-journal"><span>Error</span></section>';
    }


?>