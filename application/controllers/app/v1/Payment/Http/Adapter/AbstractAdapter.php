<?php


namespace Payment\Http\Adapter;

use Payment\Http\Client\ClientInterface;

abstract class AbstractAdapter implements AdapterInterface {

  /**
   * @var Client
   */
  protected $client;

  /**
   * @param Client $client
   */
  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  /**
   * @return Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * @return string
   */
  public function getCaBundlePath() {
    return $this->getClient()->getCaBundlePath();
  }
}
