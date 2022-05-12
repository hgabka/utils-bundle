<?php

namespace Hgabka\UtilsBundle\Google;

use Google\Client;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

class DriveDownloader
{
    /** @var HgabkaUtils */
    protected $utils;

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $clientId;

    /** @var string */
    protected $clientSecret;

    /**
     * DriveDownloader constructor.
     *
     * @param mixed $apiKey
     * @param mixed $clientId
     * @param mixed $clientSecret
     */
    public function __construct(HgabkaUtils $utils, $apiKey, $clientId, $clientSecret)
    {
        $this->utils = $utils;
        $this->apiKey = $apiKey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function createDownloadContent($fileId, $token, $forcedFileName = null)
    {
        $client = new Client();
        $client->setDeveloperKey($this->apiKey);

        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->addScope(\Google_Service_Drive::DRIVE_FILE);
        $client->addScope(\Google_Service_Drive::DRIVE);
        $client->addScope(\Google_Service_Drive::DRIVE_METADATA);
        $client->setAccessToken($token);

        $headers = ['Referer' => $this->utils->getHost()];
        $guzzleClient = new \GuzzleHttp\Client(['curl' => [\CURLOPT_SSL_VERIFYPEER => false], 'headers' => $headers]);
        $client->setHttpClient($guzzleClient);
        $service = new \Google_Service_Drive($client);

        try {
            $file = $service->files->get($fileId, ['fields' => 'size,originalFilename,mimeType,webContentLink,exportLinks,name,fileExtension,fullFileExtension']);
            $mimeType = $file->getMimeType();
            $extension = $file->getFileExtension();
            $fileName = null !== $forcedFileName ? ($forcedFileName . '.' . $extension) : $file->getOriginalFilename();
            $size = $file->getSize();
        } catch (Throwable $e) {
            $file = null;
            $content = '';
            $error = $e->getMessage();
        }

        try {
            $fileResponse = $service->files->get($fileId, ['alt' => 'media']);
            $content = $fileResponse->getBody();
            $error = null;
        } catch (Throwable $e) {
            $error = $e->getMessage();
            $fileResponse = null;
            if ($file && $file->getExportLinks()) {
                if (null !== ($data = $this->guessDataFromExportLinks($service, $fileId, $file->getExportLinks()))) {
                    ['content' => $content, 'size' => $size, 'mimeType' => $mimeType, 'extension' => $extension] = $data;

                    $fileName = ($forcedFileName ?? $file->getName()) . (empty($extension) ? '' : ('.' . $extension));
                    $error = null;
                }
            }
        }

        if (!empty($error)) {
            $errorArray = json_decode($error, true);
            $error = null === $errorArray ? $error : $errorArray;
        }

        return [
            'error' => $error ?? (empty($content) ? 'empty' : null),
            'content' => $content ?? '',
            'fileName' => $fileName ?? null,
            'size' => $size ?? null,
            'mimeType' => $mimeType ?? null,
            'extension' => $extension ?? null,
        ];
    }

    public function createDownloadResponse($fileId, $token, $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT, $forcedFileName = null)
    {
        [
            'content' => $content,
            'fileName' => $fileName,
            'size' => $size,
            'mimeType' => $mimeType,
        ] = $this->createDownloadContent($fileId, $token, $forcedFileName);

        if (empty($content)) {
            return new Response();
        }

        $response = new Response($content);
        $disposition = $this->utils->makeUtf8Disposition($disposition, $fileName);

        $response->headers->set('Content-Disposition', $disposition . '; ' . HeaderUtils::toString(['filename' => $fileName], ';'));
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $mimeType);
        $response->headers->set('Content-length', $size);

        return $response;
    }

    protected function guessDataFromExportLinks($service, $fileId, $exportLinks)
    {
        foreach (
            [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf',
                'application/text',
            ] as $mime
        ) {
            if (isset($exportLinks[$mime])) {
                $response = $service->files->export($fileId, $mime, ['alt' => 'media']);
                if (200 === $response->getStatusCode()) {
                    $content = $response->getBody()->getContents();
                    preg_match('/^(.*)\&exportFormat=(.*)$/', $exportLinks[$mime], $matches);
                    $extension = !empty($matches[2]) ? $matches[2] : '';

                    return [
                        'content' => $content,
                        'size' => \strlen($content),
                        'mimeType' => $mime,
                        'extension' => $extension,
                    ];
                }
            }
        }

        return null;
    }
}
