<?php

namespace App\Constants;

class GlobalConst {
    const USER_PASS_RESEND_TIME_MINUTE = "1";

    const ACTIVE = true;
    const BANNED = false;
    const SUCCESS = true;
    const DEFAULT_TOKEN_EXP_SEC = 3600;

    const VERIFIED = 1;
    const APPROVED = 1;
    const PENDING = 2;
    const REJECTED = 3;
    const DEFAULT = 0;
    const UNVERIFIED = 0;

    const USER      = "USER";
    const ADMIN = "ADMIN";
    const VENDOR = "VENDOR";

    const TRANSFER  = "transfer";
    const EXCHANGE  = "exchange";
    const ADD       = "add";
    const OUT       = "out";
    const PAYMENT   = "payment";

    const INVEST_PROFIT_DAILY_BASIS = "DAILY-BASIS";
    const INVEST_PROFIT_ONE_TIME = "ONE-TIME";

    const RUNNING   = 2;
    const COMPLETE  = 1;
    const CANCEL = 3;

    const INVESTMENT = "INVESTMENT";
    const PROFIT     = "PROFIT";

    const UNKNOWN = "UNKNOWN";
    const USEFUL_LINK_PRIVACY_POLICY = "PRIVACY_POLICY";
    const SYSTEM_MAINTENANCE    = "system-maintenance";
}
