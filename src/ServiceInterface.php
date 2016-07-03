<?php
namespace Bolt\Extension\DanielKulbe\Shariff;

interface ServiceInterface
{
    public function getRequest($url);
    public function extractCount($data);
    public function getName();
    public function setConfig(array $config);
}