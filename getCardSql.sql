SELECT Aow.*,pish_hikashop_product.product_name FROM
(SELECT
pish_hikashop_cart_product.cart_product_id,
pish_hikashop_cart_product.cart_id as card_product_card_id,
pish_hikashop_cart_product.product_id,
pish_hikashop_cart_product.cart_product_quantity,
pish_hikashop_cart_product.cart_product_ref_price,
pish_hikashop_cart.cart_id,
pish_hikashop_cart.cart_name

FROM pish_hikashop_cart_product
inner join
pish_hikashop_cart

ON pish_hikashop_cart_product.cart_id = pish_hikashop_cart.cart_id

WHERE  pish_hikashop_cart.user_id =0) as Aow
left join  pish_hikashop_product
on Aow.product_id = pish_hikashop_product.product_id




-- new get card info
SELECT 
 newCart.*, pish_hikashop_cart_product.cart_product_id, pish_hikashop_cart_product.cart_id AS card_product_card_id, pish_hikashop_cart_product.product_id, pish_hikashop_cart_product.cart_product_quantity, pish_hikashop_cart_product.cart_product_ref_price
FROM 
(
	SELECT  cms_users.cms_user_id
	       ,pish_hikashop_cart.cart_id
	       ,pish_hikashop_cart.user_id AS cart_user_id
	       ,pish_hikashop_cart.cart_name
	FROM 
	(
		SELECT  user_id AS cms_user_id
		FROM hyperboo_db.pish_hikashop_user
		WHERE user_cms_id = 963 
	) AS cms_users
	INNER JOIN pish_hikashop_cart
	ON cms_users.cms_user_id = pish_hikashop_cart.user_id
)as newCart
INNER JOIN pish_hikashop_cart_product
ON newCart.cart_id = pish_hikashop_cart_product.cart_id