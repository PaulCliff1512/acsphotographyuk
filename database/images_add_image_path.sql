USE photography;

ALTER TABLE images
  ADD COLUMN image_path VARCHAR(500) NULL AFTER filename;

UPDATE images
INNER JOIN topics ON images.topic_id = topics.id
SET images.image_path = CONCAT('uploads/', topics.slug, '/', images.filename)
WHERE images.image_path IS NULL;
