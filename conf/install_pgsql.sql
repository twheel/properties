create sequence properties_id_seq;

create table properties (
	id integer not null default nextval('properties_id_seq') primary key, 
	name char(48) not null, 
	type char(48) not null, 
	title char(48) not null, 
	MLS char(48) not null, 
	main_image char(48) not null, 
	images text not null, 
	documents char(48) not null, 
	teaser text not null, 
	description text not null, 
	price char(48) not null, 
	status char(48) not null, 
	address char(48) not null, 
	directions char(48) not null, 
	map char(48) not null, 
	on_hold char(48) not null 
);
