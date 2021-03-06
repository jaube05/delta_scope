<?php

namespace Drupal\delta_scope\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * DeltaScopeLogController, used for generating a report page for logs generated by this module
 */
class DeltaScopeLogController extends ControllerBase
{

    public function viewReport()
    {
        // set table headers/columns to File name, Date created and Download link
        $headers = array(
            t('File name'),
            t('Date created'),
            t('Download link'),
        );

        // loop through each of the files in the out directory outside of the site's docroot
        if (isset($_ENV['AH_SITE_ENVIRONMENT'])) {
            $dirIn = '/mnt/gfs/couchetard.' . $_ENV['AH_SITE_ENVIRONMENT'] . '/import/csv_importer/in/';
            $dirOut = '/mnt/gfs/couchetard.' . $_ENV['AH_SITE_ENVIRONMENT'] . '/import/csv_importer/out/';
        }
        else {
            $dirIn = '../import_csv/in/';
            $dirOut = '../import_csv/out/';
        }
        foreach (glob($dirOut . "*.log") as $file) {
            // create the row array
            $row = array (
                // create a data array within the row array to hold each file's info
                'data' => array(
                ),
            );

            // set each file's name without using the full path
            $filename = basename($file);
            $logType = explode('.csv', $file);
            if (strpos($logType[1], '_abandoned_scopes') !== false) {
                  // add each file's name in the array
                  $row['data']['filename'] = $filename;
                  // add each file's modification/creation date to the array
                  $row['data']['date'] = date("Y/m/d", filemtime($file));
                  // set url that corresponds to the content.sync_report_download route's path, which contains the {file} parameter
                  $url =  '<a href="/admin/reports/delta-scope-report/' . $filename . '">Download</a>';
                  // if logs are set in the private directory, the below can be used
                  // $url =  '<a href="private://logs/' . basename($file) . '">Download</a>';

                  // add each file's path location as an html link
                  $row['data']['link'] = Markup::create($url);

                  // set all the rows as arrays
                  $rows[] = $row;
            }
        }
        // sort $rows so that date is ascending
        usort ($rows, function ($a1, $a2) {
            $v1 = strtotime($a1['data']['date']);
            $v2 = strtotime($a2['data']['date']);
            return $v2 - $v1;
        }
        );
        // create the table of sorted logs
        $table = array(
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $rows,
            '#empty' => t('No reports available.'),

        );


        return $table;
    }
}
