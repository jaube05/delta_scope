<?php
//use Drupal\Core\Entity;
//use Drupal\Core\Entity\EntityManagerInterface;
//use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

/**
  * Processes the content import batch and persists the importer.
  *
  * @param $data
  *   The batch content to persist.
  * @param array $context
  *   The batch context.
  */
function processComparisonBatch($data, &$context)
{
    if (isset($data['entity_type'])) { // Get entity Type
          $entity_type = $data['entity_type'];
    }
    // Get entity bundle
    if (isset($data['bundle'])) {
          $entity_bundle = $data['bundle'];
    }
    // Pass entity bundle to Context
    $context['results']['bundle_type'] = $data['bundle'];
    // Pass filename to Context
    $context['results']['file_name'] = $data['filename'];
    // Pass filename to Context
    $context['results']['file_log_path'] = $data['filelogpath'];

    foreach ($data['values'] as $scope) {
          $context['results']['processed']['compared'][] = $scope;

          $context['sandbox']['progress']++;
    }
}
/**
 * Finish batch.
 *
 * Provide information about the Content Batch results.
 */
function finishComparisonBatch($success, $results, $operations)
{
  // Processed Log
    if (!empty($results['processed'])) {
        $date = date("d-M-Y");
        // set import type
        $entity_bundle = $results['bundle_type'];
        // set file name
        $filename = $results['file_name'];
        // set file path
        $filepath = $results['file_log_path'];
        // log name
        $logname = $filepath . $filename . "_abandoned_scopes.log";

        if (($importlog = fopen($logname, "w")) === false) {
            drupal_set_message(\Drupal::translation()->translate('Unable to create/open log file!'), 'error');
            // Send a notification about failing to create a file for the error log.
        }

        $importInfo = $date . "\n" . ucwords($entity_bundle) . " import" . "\n";
        $importInfo = $importInfo . date("Y-m-d h:i:sa") . " " . $filename;
        $importInfo = date("Y-m-d h:i:sa") . "\n" . $filename;
        //drupal_set_message(\Drupal::translation()->translate('The content was imported with errors/warnings.'), 'warning');
        fwrite($importlog, $importInfo);
        drupal_set_message($importInfo);
        // log name
        $logname = $filepath . $filename . "_abandoned_scopes.log";
        //$importlog = fopen($logname, "w") or die("Unable to open file!");
        if (($importlog = fopen($logname, "w")) === false) {
            drupal_set_message(\Drupal::translation()->translate('Unable to create/open log file!'), 'error');
            // Send a notification about failing to create a file for the error log.
        }
        //create the heading for log file
        $importInfo = date("Y-m-d h:i:sa") . "\n" . $filename;
        fwrite($importlog, $importInfo);
        drupal_set_message($importInfo);

        // if (!empty($results['processed']['inserted'])) {
        fwrite($importlog, "\n\n" . '---------------------Scope Comparison Records-------------------------' . "\n");
        // print_r($data['values']);
        foreach ($results['processed']['compared'] as $result) {
            // UI messages
            drupal_set_message(t('Scope UUID @title is in the database but not in the file', array('@title' => $result)));
            // File log messages
            fwrite($importlog, "\n" . $result);
        }
        // finish writing to log
        fclose($importlog);
    }
}
