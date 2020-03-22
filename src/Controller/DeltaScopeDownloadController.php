<?php

namespace Drupal\delta_scope\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ ResponseHeaderBag;

/**
 * An example controller.
 */
class DeltaScopeDownloadController extends ControllerBase {


    public function downloadReport($file) {

      // set the full filepath
        if(isset($_ENV['AH_SITE_ENVIRONMENT'])){
            $dirIn = '/mnt/gfs/couchetard.' . $_ENV['AH_SITE_ENVIRONMENT'] . '/import/csv_importer/in/';
            $dirOut = '/mnt/gfs/couchetard.' . $_ENV['AH_SITE_ENVIRONMENT'] . '/import/csv_importer/out/';
        }else{
            $dirIn = '../import_csv/in/';
            $dirOut = '../import_csv/out/';
        }
      $filepath = $dirOut . $file;
      $response = new BinaryFileResponse($filepath);
      $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file);
      return $response;

    }

}
