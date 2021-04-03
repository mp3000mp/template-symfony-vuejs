<?php

declare(strict_types=1);

namespace App\Helper\Request;

use App\Helper\Request\Exception\EntityValidationException;
use App\Helper\Request\Exception\JsonSchemaException;
use JsonSchema\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonRequestHelper
{
    private string $PATH_SCHEMAS;
    private SerializerInterface $serialiser;
    private Validator $jsonValidator;
    private LoggerInterface $logger;
    private ValidatorInterface $validator;

    public function __construct(string $PATH_SCHEMAS, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->PATH_SCHEMAS = $PATH_SCHEMAS;
        $this->serialiser = $serializer;
        $this->jsonValidator = new Validator();
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @template T
     *
     * @param mixed           $rawData
     * @param class-string<T> $class
     * @param T|null          $entity
     *
     * @return T
     */
    public function handleRequest($rawData, string $schema, string $class, $entity = null)
    {
        // json schema
        $jsonData = json_decode($rawData);
        $jsonSchema = json_decode(file_get_contents($this->PATH_SCHEMAS.$schema.'.json'));
        $this->jsonValidator->validate($jsonData, $jsonSchema);
        if (!$this->jsonValidator->isValid()) {
            $err = "JSON does not validate. Violations:\n";
            foreach ($this->jsonValidator->getErrors() as $error) {
                $err .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            $this->logger->error($err);
            throw new JsonSchemaException(500, 'Invalid request content.');
        }

        // entity validation
        $context = [];
        if($entity !== null){
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $entity;
        }
        $objData = $this->serialiser->deserialize($rawData, $class, 'json', $context);
        $errors = $this->validator->validate($objData);
        if(count($errors)){
            $err = "Entity does not validate. Violations:\n";
            foreach ($errors as $error) {
                $err .= sprintf("[%s=%s] %s\n", $error->getPropertyPath(), $error->getInvalidValue(), $error->getMessage());
            }
            throw new EntityValidationException(400, $err);
        }

        return $objData;
    }
}
