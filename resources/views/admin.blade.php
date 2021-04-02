<!DOCTYPE html>
<html lang="en">
<head>
    <title>asd</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url('/css/index.css')}}">
</head>
<body>
<div class="container-fluid h-100">
    <div class="container">
        <div class="row">
            <h1>Admin page</h1>
        </div>
        <!-- VOTING PERIOD -->
        <div class="row">
            <div class="col-12">
                <h3>Voting period</h3>
                <div class="row">
                    <form class="form-inline mb-2">
                        <input type="date" class="form-control mr-2" id="startdate" name="startdate" value="{{$votingperiod->start}}">
                        <input type="date" class="form-control mr-2" id="enddate" name="enddate" value="{{$votingperiod->end}}">
                        <button type="button" class="btn btn-success mr-2" onClick="setVotingPeriod()">S</button>
                        <button type="button" class="btn btn-danger mr-2" onClick="endVotingPeriod()">X</button>
                        @if($votingperiod->start==null)
                            <span>The system is <b><u>not</u></b> accepting votes</span>
                        @else
                            <span>The system is accepting votes between <b>{{$votingperiod->start}}</b> (0:01) and <b>{{$votingperiod->end}}</b> (23:59)</span>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <!-- TEACHER LIST -->
        <div class="row">
            <div class="col-12">
                <h3>Teacher list</h3>
                @foreach($teachers as $teacher)
                    <div class="row">
                        <div class="form-inline mb-2">
                            <input type="text" class="form-control mr-2" size="15" id="name{{$teacher->id}}" value="{{$teacher->name}}">
                            <input type="text" class="form-control mr-2" size="70" id="description{{$teacher->id}}" value="{{$teacher->description}}">
                            <button type="button" class="btn btn-primary mr-2" data-index="{{$teacher->id}}" onClick="modifyTeacher(this)">M</button>
                            <button type="button" class="btn btn-danger mr-2" data-index="{{$teacher->id}}" onClick="deleteTeacher(this)">X</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <form class="form-inline mb-2">
                <input type="text" class="form-control mr-2" id="newname" size="15" placeholder="Akari Saito">
                <input type="text" class="form-control mr-2" id="newdescription" size="70" placeholder="For being the best catgirl in the world <3">
                <button type="button" class="btn btn-success mr-2" onClick="addTeacher()">A</button>
            </form>
        </div>
        <!-- YOUNG TEACHER LIST -->
        <br>
        <div class="row">
            <div class="col-12">
                <h3>Young Teacher list</h3>
                @foreach($teachers_young as $teacher_young)
                    <div class="row">
                        <div class="form-inline mb-2">
                            <input type="text" class="form-control mr-2" size="15" id="name{{$teacher_young->id}}young" value="{{$teacher_young->name}}">
                            <input type="text" class="form-control mr-2" size="70" id="description{{$teacher_young->id}}young" value="{{$teacher_young->description}}">
                            <button type="button" class="btn btn-primary mr-2" data-index="{{$teacher_young->id}}" onClick="modifyTeacherYoung(this)">M</button>
                            <button type="button" class="btn btn-danger mr-2" data-index="{{$teacher_young->id}}" onClick="deleteTeacherYoung(this)">X</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row">
            <form class="form-inline mb-2">
                <input type="text" class="form-control mr-2" id="newnameyoung" size="15" placeholder="Akari Saito">
                <input type="text" class="form-control mr-2" id="newdescriptionyoung" size="70" placeholder="For being the best catgirl in the world <3">
                <button type="button" class="btn btn-success mr-2" onClick="addTeacherYoung()">A</button>
            </form>
        </div>
        <!-- pie charts -->
        <br>
        <div class="row">
            <div class="col-12">
                <h3>Pite diagram</h3>
                <div class="row">
                    <div id="piechart" style="width: 900px; height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/jquery-1.7.2.js"></script>
<script src="/js/jquery.parallax.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Name', 'Vote count'],
            @foreach($votecounts as $votecount)
            ['{{$votecount->name}}',{{$votecount->count}}],
            @endforeach
        ]);

        var options = {
            title: 'Bestest Miqo\'te',
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>
<script>
    function setVotingPeriod(){
        var startdate = document.getElementById("startdate").value;
        var enddate = document.getElementById("enddate").value;
        $.ajax({
            type: 'POST',
            data: {_token:"{{csrf_token()}}", startdate, enddate},
            url: '/setvotingperiod',
            success: function(result){
                // todo modal maybe???
                window.location.reload();
            }
        });
    }
    function endVotingPeriod(){
        $.ajax({
            type: 'POST',
            data: {_token:"{{csrf_token()}}"},
            url: '/endvotingperiod',
            success: function(result){
                window.location.reload();
            }
        })
    }

    function modifyTeacher(param){
        var teacherid = param.dataset.index;
        var teachername = document.getElementById("name"+teacherid).value;
        var teacherdescription = document.getElementById("description"+teacherid).value;
        $.ajax({
           type: 'POST',
           data: {_token: "{{csrf_token()}}", teacherid, teachername, teacherdescription},
            url: '/modifyteacher',
            success: function(result){
               window.location.reload();
            }
        });
    }
    function deleteTeacher(param){
        var teacherid = param.dataset.index;
        $.ajax({
            type: 'POST',
            data: { _token: "{{csrf_token()}}", teacherid },
            url: '/deleteteacher',
            success: function(result){
                window.location.reload();
            },
        });
    }
    function addTeacher(){
        var teachername = document.getElementById("newname").value;
        var teacherdescription = document.getElementById("newdescription").value;
        $.ajax({
            type: 'POST',
            data: { _token: "{{csrf_token()}}", teachername, teacherdescription },
            url: '/addteacher',
            success: function(result){
                document.getElementById("newname").value="";
                document.getElementById("newdescription").value="";
                window.location.reload();
            },
        });
    }
    // /////////////////////////////////////
    function modifyTeacherYoung(param){
        var teacherid = param.dataset.index;
        var teachername = document.getElementById("name"+teacherid+"young").value;
        var teacherdescription = document.getElementById("description"+teacherid+"young").value;
        $.ajax({
            type: 'POST',
            data: {_token: "{{csrf_token()}}", teacherid, teachername, teacherdescription},
            url: '/modifyteacheryoung',
            success: function(result){
                window.location.reload();
            }
        });
    }
    function deleteTeacherYoung(param){
        var teacherid = param.dataset.index;
        $.ajax({
            type: 'POST',
            data: { _token: "{{csrf_token()}}", teacherid },
            url: '/deleteteacheryoung',
            success: function(result){
                window.location.reload();
            },
        });
    }
    function addTeacherYoung(){
        var teachername = document.getElementById("newnameyoung").value;
        var teacherdescription = document.getElementById("newdescriptionyoung").value;
        $.ajax({
            type: 'POST',
            data: { _token: "{{csrf_token()}}", teachername, teacherdescription },
            url: '/addteacheryoung',
            success: function(result){
                document.getElementById("newnameyoung").value="";
                document.getElementById("newdescriptionyoung").value="";
                window.location.reload();
            },
        });
    }


</script>
</body>
</html>
