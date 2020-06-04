alter table `#__mkv_contract_items` drop foreign key `#__mkv_contract_items_#__mkv_contract_stands_standID_id_fk`;

alter table `#__mkv_contract_items`
    add constraint `#__mkv_contract_items_#__mkv_contract_stands_standID_id_fk`
        foreign key (contractStandID) references `#__mkv_contract_stands` (id)
            on update cascade on delete restrict ;

