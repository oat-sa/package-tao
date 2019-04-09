<?php

use oat\oatbox\action\Action;

class SetupKibanaDashboardAction implements Action
{
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
            CURLOPT_URL => 'oat-docker-kibana:5601/api/saved_objects/_find?type=index-pattern&search_fields=title&search=logstash*',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'kbn-version: 6.6.2',
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
            CURLOPT_URL => 'oat-docker-kibana:5601/api/saved_objects/index-pattern',
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'kbn-version: 6.6.2',
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
            CURLOPT_URL => 'oat-docker-kibana:5601/api/kibana/settings',
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'kbn-version: 6.6.2',
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
