create table delivery_note
(
    id int auto_increment
        primary key
);

create index delivery_note_id_index
    on delivery_note (id);

create table entity
(
    id    int auto_increment
        primary key,
    name  varchar(255) not null,
    alias varchar(255) not null,
    constraint entity_alias_uindex
        unique (alias),
    constraint entity_name_uindex
        unique (name)
);

create index entity_id_index
    on entity (id);

create table sender
(
    id               int auto_increment
        primary key,
    name             varchar(255) not null,
    alias            varchar(255) not null,
    medicine_manager tinyint      not null,
    constraint sender_alias_uindex
        unique (alias),
    constraint sender_name_uindex
        unique (name)
)
    collate = utf8mb4_unicode_ci;

create table picker
(
    id          int auto_increment
        primary key,
    client_id   int not null,
    preparer_id int not null,
    priority    int not null,
    constraint picker_sender_id_fk
        foreign key (client_id) references sender (id),
    constraint picker_sender_id_fk_2
        foreign key (preparer_id) references sender (id)
);

create index sender_picker_id_index
    on picker (id);

create table preparation
(
    id            int auto_increment
        primary key,
    picker_id     int                          not null,
    product       varchar(255)                 not null,
    quantity      int                          not null,
    sent_quantity int                          null,
    state         longtext collate utf8mb4_bin not null,
    last_update   datetime                     not null,
    closed        tinyint                      not null,
    constraint preparation_picker_id_fk
        foreign key (picker_id) references picker (id),
    constraint state
        check (json_valid(`state`))
);

create index preparation_id_index
    on preparation (id);

create index sender_id_index
    on sender (id);

create table stock
(
    id            int auto_increment
        primary key,
    entity_id     int          not null,
    sender_id     int          not null,
    product       varchar(255) not null,
    quantity      int          not null,
    real_quantity int          not null,
    constraint stock_entity_id_fk
        foreign key (entity_id) references entity (id),
    constraint stock_sender_id_fk
        foreign key (sender_id) references sender (id)
);

create index stock_id_index
    on stock (id);

create table transmitter
(
    id        int auto_increment
        primary key,
    entity_id int          not null,
    name      varchar(255) not null,
    alias     varchar(255) not null,
    constraint transmitter_alias_uindex
        unique (alias),
    constraint transmitter_name_uindex
        unique (name),
    constraint transmitter_entity_id_fk
        foreign key (entity_id) references entity (id)
)
    collate = utf8mb4_unicode_ci;

create table `order`
(
    id                int auto_increment
        primary key,
    transmitter_id    int                              not null,
    sender_id         int                              null,
    delivery_note_id  int                              null,
    external_id       int                              not null,
    state             varchar(255) collate utf8mb4_bin not null,
    last_update       datetime                         not null,
    closed            tinyint default 0                not null,
    forced_incomplete tinyint default 0                not null,
    constraint order_delivery_note_id_fk
        foreign key (delivery_note_id) references delivery_note (id),
    constraint order_sender_id_fk
        foreign key (sender_id) references sender (id),
    constraint order_transmitter_id_fk
        foreign key (transmitter_id) references transmitter (id)
)
    collate = utf8mb4_unicode_ci;

create index order_id_index
    on `order` (id);

create table order_row
(
    id             int auto_increment
        primary key,
    order_id       int               not null,
    preparation_id int               null,
    product        varchar(255)      not null,
    ean            varchar(255)      not null,
    quantity       int               not null,
    medicine       tinyint default 0 not null,
    serialization  text              null,
    constraint order_row_order_id_fk
        foreign key (order_id) references `order` (id),
    constraint order_row_preparation_id_fk
        foreign key (preparation_id) references preparation (id)
)
    collate = utf8mb4_unicode_ci;

create index order_row_id_index
    on order_row (id);

create index transmitter_id_index
    on transmitter (id);

create table transmitter_sender
(
    id             int auto_increment
        primary key,
    transmitter_id int not null,
    sender_id      int not null,
    priority       int not null,
    constraint transmitter_sender_sender_id_fk
        foreign key (sender_id) references sender (id),
    constraint transmitter_sender_transmitter_id_fk
        foreign key (transmitter_id) references transmitter (id)
);

create index transmitter_sender_id_index
    on transmitter_sender (id);

create
    definer = root@localhost procedure update_real_stock()
begin
    update stock s
    set real_quantity = s.quantity - IFNULL((SELECT sum(p.quantity)
                                             FROM preparation p
                                                      left join order_row r on p.id = r.preparation_id
                                                      LEFT JOIN `order` o on o.id = r.order_id
                                                      left join transmitter t on o.transmitter_id = t.id
                                             WHERE s.product = p.product
                                               AND t.entity_id = s.entity_id
                                               and p.picker_id = s.sender_id), 0) -
                        IFNULL((select sum(quantity)
                                FROM order_row r
                                         LEFT JOIN `order` o on o.id = r.order_id
                                         left join transmitter t on o.transmitter_id = t.id
                                where o.sender_id = s.sender_id
                                  and t.entity_id = s.entity_id
                                  and r.product = s.product
                                  and r.preparation_id is null), 0)
    where real_quantity = 0;
end;

create
    definer = root@localhost procedure update_real_stock_product(IN entity int, IN sender int, IN products text)
begin
    update stock s
    set real_quantity = s.quantity - IFNULL((SELECT sum(p.quantity)
                                             FROM preparation p
                                                      left join order_row r on p.id = r.preparation_id
                                                      LEFT JOIN `order` o on o.id = r.order_id
                                                      left join transmitter t on o.transmitter_id = t.id
                                             WHERE s.product = p.product
                                               AND t.entity_id = s.entity_id
                                               and p.picker_id = s.sender_id), 0) -
                        IFNULL((select sum(quantity)
                                FROM order_row r
                                         LEFT JOIN `order` o on o.id = r.order_id
                                         left join transmitter t on o.transmitter_id = t.id
                                where o.sender_id = s.sender_id
                                  and t.entity_id = s.entity_id
                                  and r.product = s.product
                                  and r.preparation_id is null), 0)
    where s.entity_id = entity
      and s.sender_id = sender
      and s.product in (products);
end;

