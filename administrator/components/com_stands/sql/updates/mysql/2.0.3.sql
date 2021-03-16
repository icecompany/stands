alter table `#__mkv_stands`
    add itemID smallint unsigned null default null after typeID,
    add index `#__mkv_stands_itemID_index` (itemID),
    add constraint `#__mkv_stands_#__mkv_price_items_itemID_id_fk`
        foreign key (itemID) references `#__mkv_price_items` (id)
            on update cascade on delete restrict;

create temporary table `#__tmp_stand_items` as
select cs.standID, ci.itemID
from `#__mkv_contract_items` ci
         left join `#__mkv_contract_stands` cs on ci.contractStandID = cs.id
         left join #__mkv_price_items pi on ci.itemID = pi.id
where ci.contractStandID is not null and pi.square_type in (1, 2, 3, 4, 5, 6, 9);

update `#__mkv_stands` s
    left join `#__tmp_stand_items` si on si.standID = s.id
set s.itemID = si.itemID;

drop table `#__tmp_stand_items`;

