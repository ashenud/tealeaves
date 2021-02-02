<html>

<head>
    <style>
        @page {
            margin: 100px 25px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            /* background-color: lightblue; */
            height: 50px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            /* background-color: lightblue; */
            height: 50px;
        }

        p {
            page-break-after: always;
        }

        p:last-child {
            page-break-after: never;
        }
        * {
        font-family: Verdana, Arial, sans-serif;
        }
        table{
            font-size: x-small;
        }
        tfoot tr td{
            font-weight: bold;
            font-size: x-small;
        }
        .gray {
            background-color: lightgray
        }
    </style>
</head>

<body>
    <header>
        <table width="100%">
            <tr>
                <td>
                    <h1>INDRA Leaf Collectors</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Wickramagiri, Pahalagama, Theppanawa, Kuruwita. 071-8015237 0453609215</h3>
                </td>
            </tr>
        </table>
        <hr>
    </header>
    <footer>
    </footer>

    <main style="margin-top: 150px">

        <table width="100%">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>Number</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>J.T.Nanawathee</td>
                        </tr>
                        <tr>
                            <td>Month</td>
                            <td>June-2020</td>
                        </tr>
                        <tr>
                            <td>Route</td>
                            <td>THEPPANAWA</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>Price (1Kg)</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>Total Tealeaves</td>
                            <td>J.T.Nanawathee</td>
                        </tr>
                        <tr>
                            <td>Total Value</td>
                            <td>June-2020</td>
                        </tr>
                        <tr>
                            <td>Collective Value</td>
                            <td>THEPPANAWA</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%">
            <thead style="background-color: lightgray;">
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price $</th>
                    <th>Total $</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Playstation IV - Black</td>
                    <td align="right">1</td>
                    <td align="right">1400.00</td>
                    <td align="right">1400.00</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td>Metal Gear Solid - Phantom</td>
                    <td align="right">1</td>
                    <td align="right">105.00</td>
                    <td align="right">105.00</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td>Final Fantasy XV - Game</td>
                    <td align="right">1</td>
                    <td align="right">130.00</td>
                    <td align="right">130.00</td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">Subtotal $</td>
                    <td align="right">1635.00</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">Tax $</td>
                    <td align="right">294.3</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">Total $</td>
                    <td align="right" class="gray">$ 1929.3</td>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>
