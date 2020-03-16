drop table if exists `#__mkv_stands`, `#__mkv_stand_catalogs`, `#__mkv_stand_pavilions`, `#__mkv_stand_square_types`, `#__pavs`;

create table `#__mkv_stand_catalogs` (
                                            id smallint unsigned not null auto_increment primary key,
                                            title varchar(255) not null,
                                            index `#__mkv_stand_catalogs_title_index` (title)
) character set utf8 collate utf8_general_ci;

create table `#__mkv_stand_pavilions` (
                                             id smallint unsigned not null auto_increment primary key,
                                             title varchar(255) not null,
                                             index `#__mkv_stand_pavilions_title_index` (title)
) character set utf8 collate utf8_general_ci comment 'Павильоны';

create table `#__mkv_stand_square_types` (
                                                id tinyint unsigned not null auto_increment primary key,
                                                title varchar(255) not null,
                                                index `#__mkv_stand_square_types_title_index` (title)
) character set utf8 collate utf8_general_ci comment 'Типы площадей';

create table `#__mkv_stands` (
                                    id int unsigned not null auto_increment primary key,
                                    catalogID smallint unsigned not null,
                                    pavilionID smallint unsigned null default null,
                                    typeID tinyint unsigned null default null,
                                    square double(6,2) unsigned not null default 0 comment 'Площадь',
                                    `number` varchar(255) not null,
                                    index `#__mkv_stands_number_index` (`number`),
                                    constraint `#__mkv_stands_#__mkv_stand_catalogs_catalogID_id_fk` foreign key (catalogID) references `#__mkv_stand_catalogs` (id) on update cascade on delete restrict,
                                    constraint `#__mkv_stands_#__mkv_stand_pavilions_pavilionID_id_fk` foreign key (pavilionID) references `#__mkv_stand_pavilions` (id) on update cascade on delete restrict,
                                    constraint `#__mkv_stands_#__mkv_stand_square_types_typeID_id_fk` foreign key (typeID) references `#__mkv_stand_square_types` (id) on update cascade on delete restrict
) character set utf8 collate utf8_general_ci;

insert into `#__mkv_stand_catalogs` select id, title from `#__prj_catalog_titles`;

create temporary table `#__pavs` as
select id, titleID, square, number, getStandPavilion(number) as pavilion
from `#__prj_catalog`
where number not like 'Демо%' and number is not null and titleID in (1,7) and number not like ('%услуги') and number not like ('%электричество') and number not like ('%соэкспонент')
order by id collate utf8_general_ci;
alter table `#__pavs` convert to character set utf8 collate utf8_general_ci;

insert into `#__mkv_stand_pavilions` select distinct null, pavilion from `#__pavs`;
insert into `#__mkv_stand_pavilions` select distinct null, number from `#__prj_catalog` where number like 'Демо%';

insert into `#__mkv_stands`
select p.id, p.titleID, pav.id, null, p.square, p.number
from `#__pavs` p
         left join `#__mkv_stand_pavilions` pav on pav.title = p.pavilion;

drop table `#__pavs`;

create temporary table `#__pavs` as
select id, titleID, square, number, number as pavilion
from `#__prj_catalog`
where number like 'Демо%'
order by id collate utf8_general_ci;
alter table `#__pavs` convert to character set utf8 collate utf8_general_ci;

insert into `#__mkv_stands`
select p.id, p.titleID, pav.id, null, p.square, p.number
from `#__pavs` p
         left join `#__mkv_stand_pavilions` pav on pav.title = p.pavilion;

drop table `#__pavs`;

create temporary table `#__pavs` as
select id, titleID, square, number
from `#__prj_catalog`
where number like ('%услуги') and number not like ('%электричество') and number not like ('%соэкспонент')
order by id collate utf8_general_ci;
alter table `#__pavs` convert to character set utf8 collate utf8_general_ci;

insert into `#__mkv_stands`
select p.id, p.titleID, null, null, p.square, p.number
from `#__pavs` p;

drop table `#__pavs`;

