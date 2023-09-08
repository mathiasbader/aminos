<?php


namespace App\Service;


use App\data\VersionInfo;
use DateTime;

class VersionsService
{
    function getVersions(): array {
        return [
            new VersionInfo("0.3", new DateTime("2023-09-08"), [
                'testTubeIconsForLearningProgress',
            ]),
            new VersionInfo("0.2", new DateTime("2021-12-26"), [
                'saveLearningProgress',
            ]),
            new VersionInfo("0.1", new DateTime("2021-10-31"), [
                'addedFlatImagesOfAminoAcids',
                'improvedTestingSystem',
                'addedLanguageSelectorOnEveryPage'
            ]),
        ];
    }
}
