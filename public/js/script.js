$(document).ready(function () {
    $(document.body).on('click', '.play-week', function (e) {
        playNextWeek(e.target.attributes['data-week']['value']);
        refreshFixture();
        refreshStanding();
    });

    $(".simulate-all-weeks").on('click', function () {
        $.get("/play-all-weeks", function () {
            refreshFixture();
            refreshStanding();
            $('.prediction-wrapper').empty();
        });
    });

    $(".reset-all").click(function () {
        $.get("reset-all", function () {
            refreshFixture();
            refreshStanding();
            $('.prediction-wrapper').empty();
        });
    });

    function refreshFixture() {
        $.get("/fixtures", function (data) {
            var showData = $('#table-body');

            showData.empty();
            showData.hide();
            $.each(data.weeks, function (i, week) {
                var html = "";
                html += "<tr><td colspan='3' style='font-weight: bold; text-align: center;background: skyblue;'>" + week.title + " Week Matches</td></tr>";
                $.each(data.items[week.id], function (i, item) {
                    html += "<tr>";
                    html += "<td style='text-align: center;'><img width='30' height='30' src='/images/" + item.home_shirt + "'>" + item.home_team + "</td>";
                    html += "<td style='text-align: center;'>" + item.home_team_goal + " - " + item.away_team_goal + "</td>";
                    html += "<td style='text-align: center;'><img width='30' height='30' src='/images/" + item.away_shirt + "'>" + item.away_team + "</td>";
                    html += "</tr>";
                });
                if (data.items[week.id][0].status == 0) {
                    html += "<tr>";
                    html += "<td colspan='5' style='border: none; text-align: center;'>";
                    html += "<button  data-week='" + week.id + "' class='btn btn-primary play-week'> Simulate " + week.title + " Week  </button>";
                    html += "</td>";
                    html += "</tr>"
                }
                showData.append(html);
            });

            showData.show('slow');
        });
    }

    function refreshStanding() {
        $.get("standings", function (data) {
            var showData = $('#standings-table tbody');
            showData.empty();
            showData.hide();
            $.each(data, function (i, item) {

                var html = "";
                html += "<tr>";
                html += "<td><img width='50' height='50' src='/images/" + item.logo + "' />" + item.name + "</td>";
                html += "<td>" + item.played + "</td>";
                html += "<td>" + item.won + "</td>";
                html += "<td>" + item.draw + "</td>";
                html += "<td>" + item.lose + "</td>";
                html += "<td>" + item.goal_drawn + "</td>";
                html += "<td>" + item.points + "</td>";
                html += "</tr>";
                showData.append(html);
            });
            showData.show('slow');

        });
    }

    function playNextWeek(weekId) {

        $.get("/play-week/" + weekId, function (data) {
            var showData = $('#weekly-matches');
            showData.empty();
            showData.hide();

            $.each(data.matches, function (i, item) {
                var html = "";
                if (i == 0) {
                    html += "<tr>"
                        + '<td colspan="3">' + item.name + ' Matches</td>'
                        + '</tr>';
                }
                html += '<tr>'
                    + '<td><img width="30" height="30" src="http://85.95.252.220:8125/images/' + item.home_logo + '" /> ' + item.home_team + '</td>'
                    + '<td><span id="home-goal" data-match-id="' + item.id + '">' + item.home_goal + '</span> - <span id="away-goal"  data-match-id="' + item.id + '">' + item.away_goal + '</span></td>'
                    + '<td><img width="30" height="30" src="http://85.95.252.220:8125/images/' + item.away_logo + '" /> ' + item.away_team + '</td>'
                    + '</tr>'
                showData.append(html);

                if (item.played == 1)
                    $('#play-weekly').hide();
            });
            showData.show('slow');

        });

        if(weekId < 6) {
            prediction();
        }else {
            $('.prediction-wrapper').empty();
        }
    }

    function prediction() {
        $.get("/prediction", function (data) {

            $('.prediction-wrapper').empty();

            var html = "";
            html +=  "<h3 style='text-align: center; margin-top:50px; width: 100%;'>Champion Prediction</h3>";
            html +=  "<table class='table table-dark' style='width: 100%;'>";
            html +=  "<thead>";
            html +=  "<tr>";
            html +=  "<th scope='col'>Team</th>";
            html +=  "<th scope='col'>Percentage</th>";
            html +=  "</tr>";
            html +=  "</thead>";
            html +=  "<tbody>";

            $.each( data.items, function (i,  data) {
                console.log(data, 'fefef');
                html +=  "<tr>";
                html +=  "<th scope='row'>" + data[0] + "</th>";
                html +=  "<td>" + data[1] + " % </td>";
                html +=  "</tr>";
            });


            html +=  "</tbody>";
            html +=  "</table>";

            $('.prediction-wrapper').append(html);

        });
    }
});

