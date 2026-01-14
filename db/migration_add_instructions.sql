-- Migration: Add instructions column for Turn By Turn Navigation
-- Date: 2026-01-13
-- Description: untuk menyimpan data turn-by-turn navigation dari OpenRouteService

ALTER TABLE `tbl_route_cache` 
ADD COLUMN `instructions` LONGTEXT NULL COMMENT 'JSON array of turn-by-turn navigation instructions' 
AFTER `duration`;