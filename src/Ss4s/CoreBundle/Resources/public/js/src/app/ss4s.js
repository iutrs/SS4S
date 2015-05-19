/**
 * @module ss4s
 * @since 1.0
 */
angular.module('ss4s', []);

/**
 * This replaces the {{ }} of Angular in order to avoid confilcts with Twig.
 *
 * @module ss4s
 * @since 1.0
 */
angular.module('ss4s')
  .config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{')
      .endSymbol('}]}');
  });