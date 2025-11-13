INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('basic', 'schema', 'schema', 'Schema', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), 'fa-brands fa-google');

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('basic', 'config', 'config', 'Config', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), "fa-solid fa-gear");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('basic', 'user', 'user', 'User', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), "fa-solid fa-user");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('menu', 'menu', 'menu', 'Menu', '', "fa-solid fa-bars");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('menu', 'menu_position', 'menu position', 'MenuPosition', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), "fa-solid fa-layer-group");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('menu', 'menu_target', 'menu target', 'MenuTarget', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), "fa-solid fa-link");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('menu', 'menu_type', 'menu type', 'MenuType', JSON_ARRAY('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'), "fa-solid fa-tag");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('blog', 'category', 'category', 'Category', '', "fa-solid fa-layer-group");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('blog', 'blog', 'blog', 'Blog', '', "fa-solid fa-newspaper");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('blog', 'tag', 'tag', 'Tag', '', "fa-solid fa-tag");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('content', 'article', 'article', 'Article', '', "fa-solid fa-newspaper");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('content', 'accordion', 'accordion', 'Accrodion', '', "fa-solid fa-list");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('blog', 'form', 'form', 'Form', '', "fa-solid fa-envelope");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('blog', 'form_type', 'form type', 'FormType', JSON_ARRAY('ROLE_SUPER_ADMIN'), "fa-solid fa-tag");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('image', 'slide', 'slide', 'Slide', '', "fa-solid fa-panorama");

INSERT INTO component (group_name, name, label, class, role, icon)
VALUES ('image', 'gallery', 'gallery', 'Gallery', '', "fa-solid fa-images");