create
    definer = root@localhost procedure update_real_stock(IN entity int, IN sender int)
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
      and s.sender_id = sender;
end;

create
    definer = root@localhost procedure update_real_stock_products(IN entity int, IN sender int, IN products text)
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

