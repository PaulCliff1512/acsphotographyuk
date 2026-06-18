USE photography;

ALTER TABLE homepage_slider_slots
  ADD COLUMN image_path VARCHAR(500) NULL AFTER image_id;
