ALTER TABLE dishes
ADD COLUMN IF NOT EXISTS dietary_labels VARCHAR(255) DEFAULT NULL AFTER image_url;

UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 1;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 2;
UPDATE dishes SET dietary_labels = 'vegan,halal,gluten-free' WHERE dish_id = 3;
UPDATE dishes SET dietary_labels = 'vegan,halal,gluten-free' WHERE dish_id = 4;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 5;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 6;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 8;
UPDATE dishes SET dietary_labels = 'vegan' WHERE dish_id = 9;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 11;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 12;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 13;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 14;
UPDATE dishes SET dietary_labels = 'vegan' WHERE dish_id = 15;
UPDATE dishes SET dietary_labels = 'vegan,halal,gluten-free' WHERE dish_id = 17;
UPDATE dishes SET dietary_labels = 'halal' WHERE dish_id = 18;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 19;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 21;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 22;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 23;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 24;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 26;
UPDATE dishes SET dietary_labels = 'halal,gluten-free' WHERE dish_id = 29;
UPDATE dishes SET dietary_labels = 'gluten-free,halal' WHERE dish_id = 30;
UPDATE dishes SET dietary_labels = 'vegan,halal,gluten-free' WHERE dish_id = 31;
