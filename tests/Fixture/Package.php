<?php

$package = <<<PACKAGE
{
    "contents": {
        "a5bfc9e07964f8dddeb95fc584cd965d": {
            "product_id": 37,
            "variation_id": 0,
            "variation": [],
            "quantity": 1,
            "line_total": 18,
            "line_tax": 2.6955,
            "line_subtotal": 18,
            "line_subtotal_tax": 2.6955,
            "line_tax_data": {
                "total": {
                    "13": 0.9,
                    "14": 1.7955
                },
                "subtotal": {
                    "13": 0.9,
                    "14": 1.7955
                }
            },
            "data": {
                "id": 37,
                "post": {
                    "ID": 37,
                    "post_author": "1",
                    "post_date": "2013-06-07 10:53:15",
                    "post_date_gmt": "2013-06-07 10:53:15",
                    "post_content": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
                    "post_title": "Happy Ninja",
                    "post_excerpt": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
                    "post_status": "publish",
                    "comment_status": "open",
                    "ping_status": "closed",
                    "post_password": "",
                    "post_name": "happy-ninja",
                    "to_ping": "",
                    "pinged": "",
                    "post_modified": "2013-06-07 10:53:15",
                    "post_modified_gmt": "2013-06-07 10:53:15",
                    "post_content_filtered": "",
                    "post_parent": 0,
                    "guid": "http:\/\/demo.woothemes.com\/woocommerce\/?post_type=product&amp;p=37",
                    "menu_order": 0,
                    "post_type": "product",
                    "post_mime_type": "",
                    "comment_count": "2",
                    "filter": "raw"
                },
                "product_type": "simple",
                "total_stock": null,
                "price": "18",
                "manage_stock": "no",
                "sku": "Dummy SKU",
                "stock_status": "instock",
                "tax_status": "taxable",
                "tax_class": "",
                "virtual": "no"
            }
        },
        "c0c7c76d30bd3dcaefc96f40275bdc0a": {
            "product_id": 50,
            "variation_id": 0,
            "variation": [],
            "quantity": 1,
            "line_total": 35,
            "line_tax": 5.2413,
            "line_subtotal": 35,
            "line_subtotal_tax": 5.2413,
            "line_tax_data": {
                "total": {
                    "13": 1.75,
                    "14": 3.4913
                },
                "subtotal": {
                    "13": 1.75,
                    "14": 3.4913
                }
            },
            "data": {
                "id": 50,
                "post": {
                    "ID": 50,
                    "post_author": "1",
                    "post_date": "2013-06-07 11:03:56",
                    "post_date_gmt": "2013-06-07 11:03:56",
                    "post_content": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
                    "post_title": "Patient Ninja",
                    "post_excerpt": "Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.",
                    "post_status": "publish",
                    "comment_status": "open",
                    "ping_status": "closed",
                    "post_password": "",
                    "post_name": "patient-ninja",
                    "to_ping": "",
                    "pinged": "",
                    "post_modified": "2013-06-07 11:03:56",
                    "post_modified_gmt": "2013-06-07 11:03:56",
                    "post_content_filtered": "",
                    "post_parent": 0,
                    "guid": "http:\/\/demo.woothemes.com\/woocommerce\/?post_type=product&amp;p=50",
                    "menu_order": 0,
                    "post_type": "product",
                    "post_mime_type": "",
                    "comment_count": "3",
                    "filter": "raw"
                },
                "product_type": "simple",
                "total_stock": null,
                "price": "35",
                "manage_stock": "no",
                "sku": "DUMMY SKU",
                "stock_status": "instock",
                "virtual": "no",
                "tax_status": "taxable",
                "tax_class": ""
            }
        }
    },
    "contents_cost": 53,
    "applied_coupons": [],
    "user": {
        "ID": 1
    },
    "destination": {
        "country": "CA",
        "state": "QC",
        "postcode": "H3E 1H2",
        "city": "Verdun",
        "address": "1460 N. MAIN STREET, # 9",
        "address_2": ""
    },
    "rates": []
}
PACKAGE;

return json_decode($package, true);
