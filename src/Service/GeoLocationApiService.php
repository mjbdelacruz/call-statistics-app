<?php

namespace App\Service;

use App\Exception\ServiceException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoLocationApiService
{
    private HttpClientInterface $httpClient;

    private LoggerInterface $logger;

    private string $geoLocationEndpoint;

    private string $geoLocationApiKey;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, string $geoLocationEndpoint, string $geoLocationApiKey)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->geoLocationEndpoint = $geoLocationEndpoint;
        $this->geoLocationApiKey = $geoLocationApiKey;
    }

    /**
     * @param array $ipAddresses
     * @return array
     */
    public function getContinentsByIpAddresses(array $ipAddresses): array
    {
        $continents = [];
        foreach ($ipAddresses AS $ipAddress) {
            $geoResponse = $this->httpClient->request('GET', sprintf('%s?apiKey=%s&ip=%s&fields=continent_code', $this->geoLocationEndpoint, $this->geoLocationApiKey, $ipAddress));
            if (200 === $geoResponse->getStatusCode()) {
                $response = $geoResponse->toArray();
                $continents[$ipAddress] = $response['continent_code'];
            } else {
                $message = sprintf('Error code: %s. Error message: %s', $response->getStatusCode(), $response->getContent());
                throw new ServiceException($message);
            }
        }

        return $continents;
    }
}