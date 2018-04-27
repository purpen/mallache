<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">
</head>

<style>
    @font-face {
        font-family: msyh;
        src: url("{{ storage_path('fonts/msyh.ttf') }}");
    }

    body, * {

        font-family: msyh, sans-serif !important;
    }

    div {
        color: #222;
    }

    .title {
        margin: 20px;
        text-align: center;
    }

    .title {
        font-size: 22px;
    }

    .line {
        border-bottom: solid #EBEBEB 1px;
        margin: 10px 0 20px 0;
    }

    .form-btn button {
        padding: 10px 40px;
        margin: 0px;
        margin-left: 20px;
    }

    p {
        line-height: 1;
    }

    .contact-box {

    }

    .contact-box p span {
        color: #666;
    }

    .sum-box {
        margin: 0 20px 50px 0;
    }

    .sum-box .tax-box {

    }

    .sum-box .tax-box p {
        float: right;
        line-height: 2.2;
        color: #222;
    }

    .total-money {
        margin-left: 10px;
    }

    .total-money span {
        color: #FF5A5F;
    }

    .invoice-box {
        clear: both;
    }

    .invoice-box p {
        float: right;
    }

    .tax-total-box {
        clear: both;
    }

    .tax-total-money {
        line-height: 1.5;
        float: right;
    }

    .tax-total-money span {
        color: #FF5A5F;
    }

    .item-box {
        display: flex;
    }

    .item-content {
        flex-grow: 1;
        margin: 0 0 20px 0;
    }

    .item-money {
        color: #FF5A5F;
        white-space: nowrap;
        margin: 10px 0 0 20px;
    }
</style>
<body>
<div class="container">

    <div class="title">
        {{ $data['project_name'] }} 项目报价
    </div>
    <div class="line"></div>
    <div style="">
        <div style="width: 50%; float: left">
            <p><span>客户（甲方）：</span>{{ $data['company_name'] }}</p>
            <p><span>联系人：</span>{{ $data['contact_name'] }}</p>
            <p><span>联系电话：</span>{{ $data['phone'] }}</p>
            <p><span>地址：</span>{{ $data['address'] }}</p>
        </div>
        <div style="width: 50%; float: left">
            <p><span>服务方（乙方）：</span>{{ $data['design_company_name'] }}</p>
            <p><span>联系人：</span>{{ $data['design_contact_name'] }}</p>
            <p><span>联系电话：</span>{{ $data['design_phone'] }}</p>
            <p><span>地址：</span>{{ $data['design_address'] }}</p>
        </div>
        <div style="clear: both"></div>
    </div>

    <div class="line"></div>
    <div class="blank20"></div>
    <div>
        项目目标:
        <p>{{ $data['summary'] }}</p>
    </div>
    <div class="line"></div>
    <div class="blank40"></div>
    <div>
        项目工作计划及费用:
        <div>
            @foreach($data['plan'] as $v1)
                <div>
                    <div style="width: 25%;float: left;">
                        <p>{{ $v1['content'] }}</p>
                    </div>

                    <div class="item-content" style="width: 50%;float: left;">
                        <p>
                            @foreach($v1['arranged'] as $v2)
                                <span v-for="(c, c_index) in d.arranged" :key="c_index">
                                    {{ $v2['number'] }}名 {{ $v2['name'] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                </span>
                            @endforeach
                        </p>
                        <p>{{ $v1['duration'] }}个 工作日</p>
                        <p>{{ $v1['summary'] }}</p>
                    </div>

                    <div class="item-money" style="width: 25%;float: left;">¥ {{$v1['price'] }}</div>
                    
                    <div class="line" style="clear: both;"></div>
                </div>
            @endforeach

        </div>
    </div>

    <div class="sum-box">
        <div class="tax-box">
            <p class="total-money">总计 @if($data['is_tax'])<span>（含税）</span>@endif： <span>¥{{ $data['total_price'] }}</span> 元
            </p>
        </div>
        @if(!$data['is_tax'])
        <div>
            <div class="tax-total-box">
                <p class="tax-total-money">税率: <span> {{ $data['tax_rate']}}%</span> &nbsp;&nbsp;&nbsp;&nbsp;总计（含税）：
                    <span>¥{{ $data['price'] }}</span> 元</p>
            </div>
        </div>
        @endif
    </div>

</div>
</body>
