-- ============================================================
-- Marketing CRM — Complete Database Schema
-- Generated: 2024-01-01
-- MySQL 8.0+ / MariaDB 10.6+
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_ENGINE_SUBSTITUTION';

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL,
  `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
  `password`          VARCHAR(255)    NULL DEFAULT NULL COMMENT 'Nullable for OAuth users',
  `role`              ENUM('admin','marketing_manager','viewer') NOT NULL DEFAULT 'viewer',
  `google_id`         VARCHAR(255)    NULL DEFAULT NULL,
  `avatar`            VARCHAR(255)    NULL DEFAULT NULL,
  `remember_token`    VARCHAR(100)    NULL DEFAULT NULL,
  `created_at`        TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`        TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: password_reset_tokens
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: sessions
-- ============================================================
CREATE TABLE `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address`    VARCHAR(45)  NULL DEFAULT NULL,
  `user_agent`    TEXT         NULL DEFAULT NULL,
  `payload`       LONGTEXT     NOT NULL,
  `last_activity` INT          NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: segments
-- ============================================================
CREATE TABLE `segments` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `name`          VARCHAR(255)    NOT NULL,
  `description`   TEXT            NULL DEFAULT NULL,
  `filters`       JSON            NULL DEFAULT NULL COMMENT 'Array of filter rules: [{field, operator, value}]',
  `is_dynamic`    TINYINT(1)      NOT NULL DEFAULT 1,
  `contact_count` INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `segments_user_id_foreign` (`user_id`),
  CONSTRAINT `segments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: campaigns
-- ============================================================
CREATE TABLE `campaigns` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         BIGINT UNSIGNED NOT NULL,
  `segment_id`      BIGINT UNSIGNED NULL DEFAULT NULL,
  `name`            VARCHAR(255)    NOT NULL,
  `type`            ENUM('email','sms','push_notification','social_media') NOT NULL DEFAULT 'email',
  `status`          ENUM('draft','scheduled','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft',
  `subject`         VARCHAR(255)    NULL DEFAULT NULL,
  `content`         LONGTEXT        NULL DEFAULT NULL,
  `budget`          DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `spent`           DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `scheduled_at`    TIMESTAMP       NULL DEFAULT NULL,
  `sent_at`         TIMESTAMP       NULL DEFAULT NULL,
  `ab_test_enabled` TINYINT(1)      NOT NULL DEFAULT 0,
  `variant_a`       LONGTEXT        NULL DEFAULT NULL,
  `variant_b`       LONGTEXT        NULL DEFAULT NULL,
  `frequency`       ENUM('one_time','daily','weekly','monthly') NOT NULL DEFAULT 'one_time',
  `created_at`      TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
  `deleted_at`      TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaigns_user_id_foreign` (`user_id`),
  KEY `campaigns_segment_id_foreign` (`segment_id`),
  CONSTRAINT `campaigns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaigns_segment_id_foreign` FOREIGN KEY (`segment_id`) REFERENCES `segments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: campaign_stats
-- ============================================================
CREATE TABLE `campaign_stats` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id`   BIGINT UNSIGNED NOT NULL,
  `opens`         INT UNSIGNED    NOT NULL DEFAULT 0,
  `clicks`        INT UNSIGNED    NOT NULL DEFAULT 0,
  `conversions`   INT UNSIGNED    NOT NULL DEFAULT 0,
  `bounces`       INT UNSIGNED    NOT NULL DEFAULT 0,
  `unsubscribes`  INT UNSIGNED    NOT NULL DEFAULT 0,
  `sent_count`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_stats_campaign_id_unique` (`campaign_id`),
  CONSTRAINT `campaign_stats_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: contacts
-- ============================================================
CREATE TABLE `contacts` (
  `id`               BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`          BIGINT UNSIGNED  NOT NULL,
  `email`            VARCHAR(255)     NOT NULL,
  `first_name`       VARCHAR(255)     NULL DEFAULT NULL,
  `last_name`        VARCHAR(255)     NULL DEFAULT NULL,
  `phone`            VARCHAR(255)     NULL DEFAULT NULL,
  `location`         VARCHAR(255)     NULL DEFAULT NULL,
  `age`              TINYINT UNSIGNED NULL DEFAULT NULL,
  `gender`           ENUM('male','female','other','prefer_not_to_say') NULL DEFAULT NULL,
  `status`           ENUM('active','unsubscribed','bounced') NOT NULL DEFAULT 'active',
  `custom_fields`    JSON             NULL DEFAULT NULL,
  `last_activity_at` TIMESTAMP        NULL DEFAULT NULL,
  `created_at`       TIMESTAMP        NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP        NULL DEFAULT NULL,
  `deleted_at`       TIMESTAMP        NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_user_id_email_unique` (`user_id`, `email`),
  KEY `contacts_email_index` (`email`),
  KEY `contacts_user_id_foreign` (`user_id`),
  CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: segment_contacts  (pivot)
-- ============================================================
CREATE TABLE `segment_contacts` (
  `segment_id` BIGINT UNSIGNED NOT NULL,
  `contact_id` BIGINT UNSIGNED NOT NULL,
  `added_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`segment_id`, `contact_id`),
  KEY `segment_contacts_contact_id_foreign` (`contact_id`),
  CONSTRAINT `segment_contacts_segment_id_foreign` FOREIGN KEY (`segment_id`) REFERENCES `segments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `segment_contacts_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: email_templates
-- ============================================================
CREATE TABLE `email_templates` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `name`         VARCHAR(255)    NOT NULL,
  `category`     VARCHAR(255)    NOT NULL DEFAULT 'general',
  `subject`      VARCHAR(255)    NOT NULL,
  `html_content` LONGTEXT        NOT NULL,
  `thumbnail`    VARCHAR(255)    NULL DEFAULT NULL,
  `is_public`    TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP       NULL DEFAULT NULL,
  `deleted_at`   TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_templates_user_id_foreign` (`user_id`),
  CONSTRAINT `email_templates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: email_events
-- ============================================================
CREATE TABLE `email_events` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id`    BIGINT UNSIGNED NOT NULL,
  `contact_id`     BIGINT UNSIGNED NULL DEFAULT NULL,
  `event_type`     ENUM('sent','open','click','bounce','unsubscribe') NOT NULL,
  `metadata`       JSON            NULL DEFAULT NULL,
  `ip_address`     VARCHAR(45)     NULL DEFAULT NULL,
  `user_agent`     TEXT            NULL DEFAULT NULL,
  `tracking_token` VARCHAR(255)    NULL DEFAULT NULL,
  `created_at`     TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_events_tracking_token_index` (`tracking_token`),
  KEY `email_events_campaign_id_event_type_index` (`campaign_id`, `event_type`),
  KEY `email_events_contact_id_event_type_index` (`contact_id`, `event_type`),
  CONSTRAINT `email_events_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_events_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: lead_forms
-- ============================================================
CREATE TABLE `lead_forms` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `name`       VARCHAR(255)    NOT NULL,
  `slug`       VARCHAR(255)    NOT NULL,
  `fields`     JSON            NOT NULL COMMENT 'Array of form field definitions',
  `settings`   JSON            NULL DEFAULT NULL,
  `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP       NULL DEFAULT NULL,
  `updated_at` TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_forms_slug_unique` (`slug`),
  KEY `lead_forms_user_id_foreign` (`user_id`),
  CONSTRAINT `lead_forms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: lead_submissions
-- ============================================================
CREATE TABLE `lead_submissions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id`    BIGINT UNSIGNED NOT NULL,
  `data`       JSON            NOT NULL COMMENT 'Submitted form field values',
  `ip_address` VARCHAR(45)     NULL DEFAULT NULL,
  `user_agent` TEXT            NULL DEFAULT NULL,
  `referrer`   VARCHAR(255)    NULL DEFAULT NULL,
  `created_at` TIMESTAMP       NULL DEFAULT NULL,
  `updated_at` TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_submissions_form_id_foreign` (`form_id`),
  CONSTRAINT `lead_submissions_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `lead_forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: ab_tests
-- ============================================================
CREATE TABLE `ab_tests` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id`         BIGINT UNSIGNED NOT NULL,
  `variant_a_subject`   VARCHAR(255)    NOT NULL,
  `variant_a_content`   LONGTEXT        NOT NULL,
  `variant_b_subject`   VARCHAR(255)    NOT NULL,
  `variant_b_content`   LONGTEXT        NOT NULL,
  `winner`              ENUM('a','b')   NULL DEFAULT NULL,
  `variant_a_opens`     INT UNSIGNED    NOT NULL DEFAULT 0,
  `variant_b_opens`     INT UNSIGNED    NOT NULL DEFAULT 0,
  `variant_a_clicks`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `variant_b_clicks`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`          TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`          TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ab_tests_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `ab_tests_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;

-- ============================================================
-- ER DIAGRAM NOTES
-- ============================================================
--
--  users ─────────────────────────────────────────────────────
--    │  1:N  segments          (user_id)
--    │  1:N  campaigns         (user_id)
--    │  1:N  contacts          (user_id)
--    │  1:N  email_templates   (user_id)
--    │  1:N  lead_forms        (user_id)
--
--  segments ──────────────────────────────────────────────────
--    │  M:N  contacts          via segment_contacts pivot
--    │  1:N  campaigns         (segment_id, nullable)
--
--  campaigns ─────────────────────────────────────────────────
--    │  1:1  campaign_stats    (campaign_id)
--    │  1:N  email_events      (campaign_id)
--    │  1:N  ab_tests          (campaign_id)
--
--  contacts ──────────────────────────────────────────────────
--    │  M:N  segments          via segment_contacts pivot
--    │  1:N  email_events      (contact_id, nullable)
--
--  lead_forms ────────────────────────────────────────────────
--    │  1:N  lead_submissions  (form_id)
--
-- ============================================================
