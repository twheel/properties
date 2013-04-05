create sequence properties_id_seq;

create table properties (
	id integer not null default nextval('properties_id_seq') primary key, 
	name char(48) not null, 
	category_id char(48) not null, 
	title char(48) not null, 
	headline_text char(48) not null, 
	image char(48) not null, 
	url char(48) not null, 
	body_text char(48) not null, 
	teaser char(48) not null, 
	template_id char(48) not null, 
	thumbnail_template_id char(48) not null, 
	directions char(48) not null, 
	show_directions char(48) not null, 
	area char(48) not null, 
	price char(48) not null, 
	status char(48) not null, 
	can_be_hero char(48) not null, 
	show_order char(48) not null, 
	brochure char(48) not null, 
	plat char(48) not null, 
	google_map char(48) not null, 
	on_hold char(48) not null 
);