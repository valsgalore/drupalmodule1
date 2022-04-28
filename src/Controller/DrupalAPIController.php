<?php

namespace Drupal\drupal_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\drupal_api\DrupalAPIManagerInterface;


/**
 * Returns responses for Drupal API routes.
 */
class DrupalAPIController extends ControllerBase {

  /**
   *  @var \Drupal\Core\DateTime\DateFormatterInterface $dateFormatter
   */
  private $dateFormatter;
  private $moduleManager;

  function __construct(\Drupal\drupal_api\Service\DrupalAPIManagerInterface $moduleManager, $dateFormatter)  {
    $this->moduleManager = $moduleManager;
    $this->dateFormatter = $dateFormatter;
  }

  //Inject services
  public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static(
      $container->get('drupal_api.manager'),
      $container->get('date.formatter'),
    );
  }


  public function latestModules() {

    $rows = [];
    $date = $this->dateFormatter;

    foreach ($this->moduleManager->getlatestModules() as $row) {
      //Create renderable array
      $rows[] = [
        '#theme' => 'drupal_api_project',
        '#project' => $row,
        '#formatted_date' => $date->format($row['created']),
      ];
    }
    //Define the cache bin for this content
    $rows['#cache'] = [
      'tags' => ['drupal_api.list'],
    ];

    return $rows;
  }

  public function latestThemes() {}

}
