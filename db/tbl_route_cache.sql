-- Create table for route caching
CREATE TABLE IF NOT EXISTS `tbl_route_cache` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `start_lat` DECIMAL(10, 7) NOT NULL,
  `start_lng` DECIMAL(10, 7) NOT NULL,
  `end_lat` DECIMAL(10, 7) NOT NULL,
  `end_lng` DECIMAL(10, 7) NOT NULL,
  `route_geometry` LONGTEXT NOT NULL COMMENT 'Encoded polyline geometry',
  `distance` INT NOT NULL COMMENT 'Distance in meters',
  `duration` DECIMAL(10, 2) NOT NULL COMMENT 'Duration in seconds',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_route` (`start_lat`, `start_lng`, `end_lat`, `end_lng`),
  KEY `idx_created` (`created_at`),
  KEY `idx_coordinates` (`start_lat`, `start_lng`, `end_lat`, `end_lng`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cache for OpenRouteService API responses';

-- Optional: Create cleanup event to delete old cache (runs daily)
-- Uncomment if you want automatic cleanup
-- CREATE EVENT IF NOT EXISTS `cleanup_old_route_cache`
-- ON SCHEDULE EVERY 1 DAY
-- DO
-- DELETE FROM tbl_route_cache WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);