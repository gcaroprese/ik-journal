<?php
/*
    
    Journal Template
    Author: S. Gabriel Caroprese
    Inforket - 09/01/2021

*/
if ( ! defined('ABSPATH')) exit('restricted access');
?>
<section id="ik-journal-search-results">
<?php
$search_results_journals = ik_journal_search_results($searchedJournal);

//Cantidad de resultados que se pueden mostrar
$searchResultsQuantity = 24; //+1 porque comienza de cero

if ($search_results_journals == NULL){
    //I show not found message
    ik_journal_result_not_found($current_pageURL);
} else {
    echo '<div id="ik_search_loading"><img style="width: 50%;max-width: 100px;text-align: center;margin: 0 auto;display: block;" src="'.plugin_dir_url( __DIR__ ).'/img/loading.gif" alt="loading journal" /></div>';

    //Search results empty. If something found the variable will change
    $searchResults = '';
    $resultFound[] = "";

    foreach ($search_results_journals as $journalFound){

        $monthJournalSearch = $journalFound->month;
        $yearJournalSearch = $journalFound->year;
        $cantidaddeQuotes = maybe_unserialize($journalFound->quote_ns); // Es una forma de contar la cantidad de journals contados
        
        if (is_array($cantidaddeQuotes)){
            $countJournalsThisResult = count($cantidaddeQuotes);
        } else {
            $countJournalsThisResult = 1;
        }
        
        //Creo variables de las diferentes preguntas del journal
        $thankfull_q = maybe_unserialize($journalFound->thankfull_q); // Es una forma de contar la cantidad de journals contados
        $todayGreat_q = maybe_unserialize($journalFound->todayGreat_q); // Es una forma de contar la cantidad de journals contados
        $daily_affirmation = maybe_unserialize($journalFound->daily_affirmation); // Es una forma de contar la cantidad de journals contados
        $important_task = maybe_unserialize($journalFound->important_task); // Es una forma de contar la cantidad de journals contados
        $amazing_happenings = maybe_unserialize($journalFound->amazing_happenings); // Es una forma de contar la cantidad de journals contados
        $today_better = maybe_unserialize($journalFound->today_better); // Es una forma de contar la cantidad de journals contados
    
        // Chequeo los registros de los diferentes días
        $keysRecords = array_keys(maybe_unserialize($journalFound->quote_ns)); // Es una forma de contar la cantidad de journals contados
   
        $DaysResultCounter = 0;
        
  
        while ($countJournalsThisResult > $DaysResultCounter && $DaysResultCounter < $searchResultsQuantity){
            if (isset($thankfull_q[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal)){
                $thanksRecord = ik_journal_findkeyword_inarray($thankfull_q[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
                //Esto es para identificar el campo donde se encontró el texto y mostrarlo en los resultados
                $campoEncontrado = $thankfull_q;
            } else {
                $thanksRecord = NULL;
            }
            if (isset($todayGreat_q[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal)){
                $todayGreat_qRecord = ik_journal_findkeyword_inarray($todayGreat_q[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
                $campoEncontrado = $todayGreat_q;
            } else {
                $todayGreat_qRecord = NULL;
            }
            if (isset($daily_affirmation[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal)){
                $campoEncontrado = $daily_affirmation;
                $daily_affirmationRecord = ik_journal_findkeyword_inarray($daily_affirmation[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
            } else {
                $daily_affirmationRecord = NULL;
            }            
            if (isset($important_task[$keysRecords[$DaysResultCounter]]['record'])){
                $important_taskRecord = ik_journal_findkeyword_inarray($important_task[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
                $campoEncontrado = $important_task;
            } else {
                $important_taskRecord = NULL;
            }           
            if (isset($amazing_happenings[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal)){
                $amazing_happeningsRecord = ik_journal_findkeyword_inarray($amazing_happenings[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
                $campoEncontrado = $amazing_happenings;
            } else {
                $amazing_happeningsRecord = NULL;
            }            
            if (isset($today_better[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal)){
                $today_betterRecord = ik_journal_findkeyword_inarray($today_better[$keysRecords[$DaysResultCounter]]['record'], $searchedJournal);
                $campoEncontrado = $today_better;
            } else {
                $today_betterRecord = NULL;
            }

            
            if ($searchedJournal == 'journals' || $thanksRecord == true || $todayGreat_qRecord == true || $daily_affirmationRecord == true || $important_taskRecord == true || $amazing_happeningsRecord == true || $today_betterRecord == true){
                                
                if ($thankfull_q[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $thankfull_q[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } else if ($todayGreat_q[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $todayGreat_q[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } else if ($daily_affirmation[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $daily_affirmation[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } else if ($important_task[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $important_task[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } else if ($amazing_happenings[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $amazing_happenings[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } else if ($today_better[$keysRecords[$DaysResultCounter]]['datetime'] != NULL){
                    $fechaEncontrado = $today_better[$keysRecords[$DaysResultCounter]]['datetime']->format('d-m-Y');
                } 
            }

            if (isset($fechaEncontrado)){
                //I check if it wasn't showed before
                if (!in_array($fechaEncontrado, $resultFound)){
                    //I validate the date if date is part of the search and I show the result depending on that
                    if (isset($_GET['jday']) || isset($_GET['jmonth']) || isset($_GET['jyear'])){
                        if ($searchedJournalDay != '' || $searchedJournalMonth != '' || $searchedJournalYear != ''){
                            if (date('Y', strtotime($fechaEncontrado)) == $searchedJournalYear && $searchedJournalDay == '' && $searchedJournalMonth == ''){
                                $resultAdded = true;
                            } else if (date('m', strtotime($fechaEncontrado)) == $searchedJournalMonth && $searchedJournalYear == '' && $searchedJournalDay == ''){
                                $resultAdded = true;
                            } else if (date('d', strtotime($fechaEncontrado)) == $searchedJournalDay && $searchedJournalMonth == '' && $searchedJournalYear == ''){
                                $resultAdded = true;                
                            } else if (date('d', strtotime($fechaEncontrado)) == $searchedJournalDay && date('Y', strtotime($fechaEncontrado)) == $searchedJournalYear && $searchedJournalMonth == ''){
                                $resultAdded = true;               
                            } else if (date('Y', strtotime($fechaEncontrado)) == $searchedJournalYear && date('m', strtotime($fechaEncontrado)) == $searchedJournalMonth && $searchedJournalDay == ''){
                                $resultAdded = true;                
                            } else if (date('d', strtotime($fechaEncontrado)) == $searchedJournalDay && date('m', strtotime($fechaEncontrado)) == $searchedJournalMonth && date('Y', strtotime($fechaEncontrado)) == $searchedJournalYear){
                                $resultAdded = true;      
                            } else {
                                $resultAdded = false;
                            }
                        } else {
                            //No coinciden las fechas con lo buscado
                            $resultAdded = false;
                        }
                    } else {
                        //No estamos buscando por fecha. Obvio el filtro.
                        $resultAdded = true;
                    }
                } else {
                    $resultAdded = false;
                }
            } else {
                $resultAdded = false;               
            }
            if ($resultAdded == true){
                $resultFound[] = $fechaEncontrado;
                $searchResults .= '<div class="ik_journal_listed_search">
                            <a href="'.$current_pageURL.'?journal='.strtotime($fechaEncontrado).'"><b>'.$fechaEncontrado.'</b>
                            <span class="ik_journal_texto_found">'.substr($campoEncontrado[$keysRecords[$DaysResultCounter]]['record'], 0, 200).'</span>
                            </a>
                    </div>';
            }
            $DaysResultCounter = $DaysResultCounter + 1;
        }
    }

    if ($searchResults == ''){
        //I show not found message
        echo '<div id="ik_search_results_journals" style="display: none">'.ik_journal_result_not_found($current_pageURL).'</div>';
    } else {
        $searchResultsCountFound = count($resultFound)-1;
        if ($searchResultsCountFound > 1){
            $resutsText = __( 'resultados', 'ik-journal' );
        } else {
            $resutsText = __( 'resultado', 'ik-journal' );
        }
?>
    <div id="ik_search_results_journals" style="display: none">
        <p><?php _e( 'Mostrando', 'ik-journal' ); ?> <?php echo $searchResultsCountFound; ?> <?php echo $resutsText; ?>.</p>
        <div id="ik-journal-search-results"><?php echo $searchResults; ?></div>
        <div class="ik-journal-return"> <?php echo '<a href="'.$current_pageURL.'">'.__( 'Volver al journal actual', 'ik-journal').'</a>'; ?></div>
    </div>
<?php
    }
}
?>
<script>
    setTimeout(function(){ 
        jQuery('#ik_search_loading').fadeOut(400);
        setTimeout(function(){ 
            jQuery('#ik_search_results_journals').fadeIn(400);            
        }, 500);
        }, 3000);
</script>
</section>