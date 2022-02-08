<?php


namespace Initxlab\Ngd\Params;


/**
 * Centralized API constants to avoid hard-coding and typos
 * Class C for Constants
 * @package Initxlab\Ngd\Params
 */
class C
{
    public const R_PRODUCT_LINE = "product_line:read";
    public const W_PRODUCT_LINE = "product_line:write";

    public const SWAGGER_W_LABEL_PRODUCT_LINE = "Write-Product";
    public const SWAGGER_R_LABEL_PRODUCT_LINE = "Read-Product";

    public const ITEM_GET_PRODUCT_LINE = "product_line:item:get";

    public const W_WAREHOUSE = "warehouse:write";
    public const R_WAREHOUSE = "warehouse:read";
    public const ITEM_GET_WAREHOUSE = "warehouse:item:get";

    public const SWAGGER_W_LABEL_WAREHOUSE = "Write-Warehouse";
    public const SWAGGER_R_LABEL_WAREHOUSE = "Read-Warehouse";

    public const _GET = "GET";
    public const _POST = "POST";
    public const _DELETE = "DELETE";
    public const _PUT = "PUT";

    public const GROUPS = "groups";
    public const FORMATS = "formats";

    public const F_JSONLD = "jsonld";
    public const F_JSON = "json";
    public const F_HTML = "html";
    public const F_CSV = "csv";
    public const F_JSONHAL = "jsonhal";

    public const MIME_TXT_CSV = "text/csv";

    public const SWAGGER_DEFINITION_NAME = "swagger_definition_name";
    public const PAGINATION_PER_PAGE = "pagination_items_per_page";
    public const NORMALIZATION_CONTEXT = "normalization_context";

    public const SHORT_NAME_PRODUCT_LINE = "Product";
    public const SHORT_NAME_WAREHOUSE = "Warehouse";

    public const PROP_IS_PUBLISHED = "isPublished";
    public const PROP_STATS_WAREHOUSE = "countStock";
    public const PROP_NAME = "name";
    public const PROP_PRODUCT = "product";
    public const PROP_DESC = "description";

    public const MATCH_PARTIAL = "partial";
    public const MATCH_EXACT = "exact";
}