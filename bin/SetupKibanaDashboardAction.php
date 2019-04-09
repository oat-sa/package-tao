<?php

use oat\oatbox\action\Action;

class SetupKibanaDashboardAction implements Action
{
    const KIBANA_DOCKER_HOST = 'oat-docker-kibana:5601';
    const KIBANA_VERSION = '6.6.2';

    public function __invoke($params)
    {
        $indexPatternId = $this->findLogstashIndexPatternId();

        if (!$indexPatternId) {
            $indexPatternId = $this->createLogstashIndexPattern();
        }

        $this->setIndexPatternAsDefault($indexPatternId);
    }

    private function findLogstashIndexPatternId()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf(
                '%s/api/saved_objects/_find?type=index-pattern&search_fields=title&search=logstash*',
                self::KIBANA_DOCKER_HOST
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                sprintf('kbn-version: %s', self::KIBANA_VERSION),
            ],
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            echo 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
        }

        $decodedResponse = json_decode($response, true);

        curl_close($curl);

        $numberOfIndexPatterns = (int)$decodedResponse['total'];
        if ($numberOfIndexPatterns === 0) {
            return null;
        }

        if ($numberOfIndexPatterns > 1) {
            throw new Exception(
                sprintf('Number of created Kibana index patterns is %s, 1 expected.', $numberOfIndexPatterns)
            );
        }

        return $decodedResponse['saved_objects'][0]['id'];
    }

    private function createLogstashIndexPattern()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf(
                '%s/api/saved_objects/index-pattern',
                self::KIBANA_DOCKER_HOST
            ),
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                sprintf('kbn-version: %s', self::KIBANA_VERSION),
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'attributes' => [
                    'title' => 'logstash-*',
                    'timeFieldName' => '@timestamp',
                ],
            ]),
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            echo 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
        }

        $decodedResponse = json_decode($response, true);

        curl_close($curl);

        return $decodedResponse['id'];
    }

    private function setIndexPatternAsDefault($indexPatternId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf(
                '%s/api/kibana/settings',
                self::KIBANA_DOCKER_HOST
            ),
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                sprintf('kbn-version: %s', self::KIBANA_VERSION),
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'changes' => [
                    'defaultIndex' => $indexPatternId,
                ],
            ]),
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            echo 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
        }

        curl_close($curl);
    }
}
