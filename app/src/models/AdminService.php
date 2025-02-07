<?php

namespace App\Models;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php';

use App\Utils\DBConnection;

class AdminService extends DBConnection {
    private string $name;
    private string $url;
    private string $description;
    private string $icon;

    private function __construct(string $name, string $url, string $description, string $icon) {
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
        $this->icon = $icon;
    }

    public static function newAdminServiceInstance(string $name, ?string $url = null, ?string $description = null, ?string $icon = null) : AdminService {
        return new AdminService($name, $url ? : "", $description ? : "This service does not have any description yet.", $icon ? : "terminal");
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of url
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the value of icon
     */ 
    public function getIcon()
    {
        return $this->icon;
    }
}