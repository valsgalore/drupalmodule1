<?php

namespace Drupal\drupal_api\Service;

interface DrupalAPIManagerInterface {

  /**
   * @return array
   */
  function getLatestModules();

  /**
   * @return array
   */
  public function getLatestThemes();

  /**
   * Fetch the latest projects from Drupal.org
   */
  public function fetchLatestProjects();

}
