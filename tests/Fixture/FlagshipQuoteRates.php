<?php

$rates = <<<RATES
[
    {
        "price": {
            "charges": {
                "freight": 16.16
            },
            "subtotal": 16.16,
            "total": 18.58,
            "taxes": {
                "gst": 0.81,
                "qst": 1.61
            }
        },
        "service": {
            "flagship_code": "standard",
            "courier_code": "11",
            "courier_desc": "UPS Standard",
            "courier_name": "UPS",
            "transit_time": "1",
            "estimated_delivery_date": "2016-09-14 23:30:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 16.34
            },
            "subtotal": 16.34,
            "total": 18.79,
            "taxes": {
                "gst": 0.82,
                "qst": 1.63
            }
        },
        "service": {
            "flagship_code": "expressAm",
            "courier_code": "65",
            "courier_desc": "UPS Express Saver",
            "courier_name": "UPS",
            "transit_time": "1",
            "estimated_delivery_date": "2016-09-14 15:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 26.01
            },
            "subtotal": 26.01,
            "total": 29.9,
            "taxes": {
                "gst": 1.3,
                "qst": 2.59
            }
        },
        "service": {
            "flagship_code": "express",
            "courier_code": "01",
            "courier_desc": "UPS Express",
            "courier_name": "UPS",
            "transit_time": "1",
            "estimated_delivery_date": "2016-09-14 10:30:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 34.75,
                "fuel_surcharge": 2.09
            },
            "subtotal": 36.84,
            "total": 42.36,
            "taxes": {
                "gst": 1.84,
                "qst": 3.68
            }
        },
        "service": {
            "flagship_code": "expressEarlyAm",
            "courier_code": "FIRST_OVERNIGHT",
            "courier_desc": "First Overnight",
            "courier_name": "FedEx",
            "transit_time": null,
            "estimated_delivery_date": "2016-09-14 10:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 20.79,
                "fuel_surcharge": 1.24
            },
            "subtotal": 22.03,
            "total": 25.32,
            "taxes": {
                "gst": 1.1,
                "qst": 2.19
            }
        },
        "service": {
            "flagship_code": "expressAm",
            "courier_code": "PRIORITY_OVERNIGHT",
            "courier_desc": "Priority Overnight",
            "courier_name": "FedEx",
            "transit_time": null,
            "estimated_delivery_date": "2016-09-14 12:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 19.78,
                "fuel_surcharge": 1.18
            },
            "subtotal": 20.96,
            "total": 24.1,
            "taxes": {
                "gst": 1.05,
                "qst": 2.09
            }
        },
        "service": {
            "flagship_code": "expressAm",
            "courier_code": "STANDARD_OVERNIGHT",
            "courier_desc": "Standard Overnight",
            "courier_name": "FedEx",
            "transit_time": null,
            "estimated_delivery_date": "2016-09-14 17:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 19.78,
                "fuel_surcharge": 1.18
            },
            "subtotal": 20.96,
            "total": 24.1,
            "taxes": {
                "gst": 1.05,
                "qst": 2.09
            }
        },
        "service": {
            "flagship_code": "secondDay",
            "courier_code": "FEDEX_2_DAY",
            "courier_desc": "2 Days",
            "courier_name": "FedEx",
            "transit_time": null,
            "estimated_delivery_date": "2016-09-14 17:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 19.78,
                "fuel_surcharge": 1.18
            },
            "subtotal": 20.96,
            "total": 24.1,
            "taxes": {
                "gst": 1.05,
                "qst": 2.09
            }
        },
        "service": {
            "flagship_code": "express",
            "courier_code": "FEDEX_EXPRESS_SAVER",
            "courier_desc": "Economy",
            "courier_name": "FedEx",
            "transit_time": null,
            "estimated_delivery_date": "2016-09-14 17:00:00"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 24.71,
                "residential_surcharge": 3.7,
                "fuel_surcharge": 1.48
            },
            "subtotal": 29.89,
            "total": 34.37,
            "taxes": {
                "gst": 1.5,
                "qst": 2.98
            }
        },
        "service": {
            "flagship_code": "expressEarlyAm",
            "courier_code": "PurolatorExpress9AM",
            "courier_desc": "Purolator Express 9AM",
            "courier_name": "Purolator",
            "transit_time": 1,
            "estimated_delivery_date": "2016-09-14"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 17.21,
                "residential_surcharge": 3.7,
                "fuel_surcharge": 1.03
            },
            "subtotal": 21.94,
            "total": 25.23,
            "taxes": {
                "gst": 1.1,
                "qst": 2.19
            }
        },
        "service": {
            "flagship_code": "expressAm",
            "courier_code": "PurolatorExpress10:30AM",
            "courier_desc": "Purolator Express 10:30 AM",
            "courier_name": "Purolator",
            "transit_time": 1,
            "estimated_delivery_date": "2016-09-14"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 6.78,
                "residential_surcharge": 3.7,
                "fuel_surcharge": 0.4
            },
            "subtotal": 10.88,
            "total": 12.52,
            "taxes": {
                "gst": 0.55,
                "qst": 1.09
            }
        },
        "service": {
            "flagship_code": "express",
            "courier_code": "PurolatorExpress",
            "courier_desc": "Purolator Express",
            "courier_name": "Purolator",
            "transit_time": 1,
            "estimated_delivery_date": "2016-09-14"
        }
    },
    {
        "price": {
            "charges": {
                "freight": 10.7,
                "residential_surcharge": 3.7,
                "fuel_surcharge": 0.64
            },
            "subtotal": 15.04,
            "total": 17.3,
            "taxes": {
                "gst": 0.76,
                "qst": 1.5
            }
        },
        "service": {
            "flagship_code": "standard",
            "courier_code": "PurolatorGround",
            "courier_desc": "Purolator Ground",
            "courier_name": "Purolator",
            "transit_time": 1,
            "estimated_delivery_date": "2016-09-14"
        }
    }
]
RATES;

return json_decode($rates, true);
