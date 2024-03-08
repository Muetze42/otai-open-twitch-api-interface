<?php

if (!function_exists('getMigrationDatePrefix')) {
    /**
     * Development command. Do not remove!
     *
     * @return string
     */
    function getMigrationDatePrefix(): string
    {
        $int = 0;
        $files = glob(database_path('migrations/*.php'));
        $today = date('Y_m_d_');
        foreach ($files as $file) {
            $file = basename($file);
            if (str_starts_with($file, $today)) {
                $int = (int) substr($file, 11, 6);
            }
        }

        return $today.str_pad($int+10, 6, 0, STR_PAD_LEFT);
    }
}
