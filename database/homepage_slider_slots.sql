USE photography;

CREATE TABLE IF NOT EXISTS homepage_slider_slots (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  topic_id INT UNSIGNED NOT NULL,
  slot_number TINYINT UNSIGNED NOT NULL,
  image_id INT UNSIGNED NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_topic_slot (topic_id, slot_number),
  CONSTRAINT fk_homepage_slider_slots_topic
    FOREIGN KEY (topic_id) REFERENCES topics(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_homepage_slider_slots_image
    FOREIGN KEY (image_id) REFERENCES images(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO homepage_slider_slots (topic_id, slot_number)
SELECT topics.id, slot_numbers.slot_number
FROM topics
CROSS JOIN (
  SELECT 1 AS slot_number
  UNION ALL SELECT 2
  UNION ALL SELECT 3
) AS slot_numbers
WHERE topics.slug IN ('landscapes', 'creatures')
ON DUPLICATE KEY UPDATE slot_number = VALUES(slot_number);
