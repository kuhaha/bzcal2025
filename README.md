Calendar
Year extends RangeZ, months/weeks/days
Month extends RangeZ, weeks/days
Day extends RangeZ, hours/minuts/seconds

Holiday

RangeZ
  zrange ()
    - start
    - limit
    - step

DayFilter
  comparison
  - eq
  - leq
  - geq
  - lt
  - gt
  - between

  logical
  - any / or
  - all / and

　tagging
  - `y-m-d`: [<tag>]
    - <tag>
    tag,     name
    weekday  'Monday'
    weekend  'Sunday'
    weekday  'Friday'
    weekend  'Saturday'
    holiday  '成人の日'
    holiday  '秋分の日'
    holiday  '天皇誕生日'
    offday   '定休日'
    bizday   '営業日'
  - is XXX()
    XXX: Weekday, Weekend, Holiday, Bizday, Offday



  