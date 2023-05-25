<table>
    <thead>
        <tr>
            <th><b>NOMBRE</b></th>
            <th><b>CORREO ELECTRÓNICO</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['email'] }}</td>
                <td><b>NIVEL</b></td>
                <td><b>CORRECTAS</b></td>
                <td><b>TOTAL</b></td>
                <td><b>CALIFICACIÓN</b></td>
            </tr>
            @forelse($user['advances'] as $advance)
                <tr>
                    <td></td><td></td>
                    <td>{{ $advance->level->level }}</td>
                    <td>{{ $advance->correct }}</td>
                    <td>{{ $advance->total }}</td>
                    <td>
                        @if($advance->correct > 0)
                            {{ (($advance->correct / $advance->total) * 100) > 0 ? round(($advance->correct / $advance->total) * 100):0  }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td></td><td></td><td>NA</td><td>NA</td><td>NA</td><td>NA</td>
                </tr>
            @endforelse
            <tr>
                <td></td><td></td>
                <td><b>SKILL</b></td>
                <td><b>CORRECTAS</b></td>
                <td><b>TOTAL</b></td>
                <td><b>CALIFICACIÓN</b></td>
            </tr>
            @forelse($user['skills'] as $skill)
                <tr>
                    <td></td><td></td>
                    <td>{{ $skill->categorie }}</td>
                    <td>{{ $skill->categorie == 'Speaking' ? ($skill->points / 100) : $skill->points }}</td>
                    <td>{{ $skill->categorie == 'Speaking' ? ($skill->total / 100) : $skill->total }}</td>
                    <td>
                        @if($skill->points > 0)
                            {{ (($skill->points / $skill->total) * 100) > 0 ? round(($skill->points / $skill->total) * 100, 0):0  }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td></td><td></td><td>NA</td><td>NA</td><td>NA</td><td>NA</td>
                </tr>
            @endforelse
        @endforeach
    </tbody>
</table>
