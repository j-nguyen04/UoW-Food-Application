<?php

namespace App\Services;

use PDO;

class DietaryService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function ensureSchema(): void
    {
        static $schemaReady = false;

        if ($schemaReady) {
            return;
        }

        $columnCheck = $this->pdo->query("SHOW COLUMNS FROM dishes LIKE 'dietary_labels'");
        $columnExists = $columnCheck->fetch(PDO::FETCH_ASSOC) !== false;

        if (!$columnExists) {
            $this->pdo->exec("ALTER TABLE dishes ADD COLUMN dietary_labels VARCHAR(255) DEFAULT NULL AFTER image_url");
        }

        // Seed known dishes once so the filter is useful immediately on the existing menu.
        // New or edited dishes can still be updated later in the database/admin tooling.
        $seedMap = [
            1 => 'halal',
            2 => 'halal,gluten-free',
            3 => 'vegan,halal,gluten-free',
            4 => 'vegan,halal,gluten-free',
            5 => 'halal',
            6 => 'halal',
            8 => 'halal',
            9 => 'vegan',
            11 => 'halal',
            12 => 'halal,gluten-free',
            13 => 'halal',
            14 => 'halal',
            15 => 'vegan',
            17 => 'vegan,halal,gluten-free',
            18 => 'halal',
            19 => 'halal,gluten-free',
            21 => 'halal,gluten-free',
            22 => 'halal,gluten-free',
            23 => 'halal,gluten-free',
            24 => 'halal,gluten-free',
            26 => 'halal,gluten-free',
            29 => 'halal,gluten-free',
            30 => 'gluten-free,halal',
            31 => 'vegan,halal,gluten-free',
        ];

        $updateStmt = $this->pdo->prepare(
            "UPDATE dishes
             SET dietary_labels = :dietary_labels
             WHERE dish_id = :dish_id
               AND (dietary_labels IS NULL OR dietary_labels = '')"
        );

        foreach ($seedMap as $dishId => $labels) {
            $updateStmt->execute([
                'dietary_labels' => $labels,
                'dish_id' => $dishId,
            ]);
        }

        $schemaReady = true;
    }

    public function options(): array
    {
        return [
            'vegan' => 'Vegan',
            'halal' => 'Halal',
            'gluten-free' => 'Gluten-Free',
        ];
    }

    public function labels(?string $dietaryLabels): array
    {
        if ($dietaryLabels === null || trim($dietaryLabels) === '') {
            return [];
        }

        $availableOptions = $this->options();
        $labels = array_map('trim', explode(',', strtolower($dietaryLabels)));
        $displayLabels = [];

        // Convert stored normalized labels into the display labels used by the UI.
        foreach ($labels as $label) {
            if ($label !== '' && isset($availableOptions[$label])) {
                $displayLabels[] = $availableOptions[$label];
            }
        }

        return array_values(array_unique($displayLabels));
    }
}
