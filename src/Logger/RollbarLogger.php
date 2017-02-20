<?php

namespace Drupal\rollbar\Logger;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Rollbar;
use Level as RollbarLogLevel;

/**
 * Redirects logging messages to syslog.
 */
class RollbarLogger implements LoggerInterface {
  use RfcLoggerTrait;

  /**
   * A configuration object containing syslog settings.
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
  private $verbosityLevelMap = array(
    RfcLogLevel::EMERGENCY => RollbarLogLevel::CRITICAL,
    RfcLogLevel::ALERT => RollbarLogLevel::CRITICAL,
    RfcLogLevel::CRITICAL => RollbarLogLevel::CRITICAL,
    RfcLogLevel::ERROR => RollbarLogLevel::ERROR,
    RfcLogLevel::WARNING => RollbarLogLevel::WARNING,
    RfcLogLevel::NOTICE => RollbarLogLevel::INFO,
    RfcLogLevel::INFO => RollbarLogLevel::INFO,
    RfcLogLevel::DEBUG => RollbarLogLevel::DEBUG,
  );

  /**
   * Constructs a SysLog object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory object.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LogMessageParserInterface $parser) {
    $this->config = $config_factory->get('rollbar.settings');
    $this->parser = $parser;
    Rollbar::init(['access_token' => $this->config->get('access_token')]);
  }

  /**
   * {@inheritdoc}
   */
  public function log($level, $message, array $context = array()) {
    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->parser->parseMessagePlaceholders($message, $context);
    $message = empty($message_placeholders) ? $message : strtr($message, $message_placeholders);
    Rollbar::report_message($message, $this->verbosityLevelMap[$level]);
  }

}
