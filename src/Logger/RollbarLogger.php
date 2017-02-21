<?php

namespace Drupal\rollbar\Logger;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Rollbar\Rollbar;
use Rollbar\Payload\Level as RollbarLogLevel;

/**
 * Redirects logging messages to Rollbar.
 */
class RollbarLogger implements LoggerInterface {
  use RfcLoggerTrait;

  /**
   * A configuration object containing rollbar settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * @var array
   */

  /**
   * Constructs a Rollbar object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory object.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LogMessageParserInterface $parser) {
    $this->config = $config_factory->get('rollbar.settings');
    $this->parser = $parser;
    Rollbar::init(['access_token' => $this->config->get('access_token'), 'environment' => $this->config->get('environment')]);
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    $level_map = array(
      RfcLogLevel::EMERGENCY => RollbarLogLevel::critical(),
      RfcLogLevel::ALERT =>  RollbarLogLevel::critical(),
      RfcLogLevel::CRITICAL =>  RollbarLogLevel::critical(),
      RfcLogLevel::ERROR =>  RollbarLogLevel::error(),
      RfcLogLevel::WARNING =>  RollbarLogLevel::warning(),
      RfcLogLevel::NOTICE =>  RollbarLogLevel::info(),
      RfcLogLevel::INFO =>  RollbarLogLevel::info(),
      RfcLogLevel::DEBUG =>  RollbarLogLevel::debug(),
    );

    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);
    $message = empty($message_placeholders) ? $message : strtr($message, $message_placeholders);
    Rollbar::log($message, $context, $level_map[$level]);
  }

}
