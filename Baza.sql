Table uporabniki {
  id int [pk, increment]
  ime varchar(45) [not null]
  priimek varchar(45) [not null]
  uporabnisko_ime varchar(45) [not null, unique]
  email varchar(45) [not null, unique]
  geslo varchar(255) [not null]
  vloga enum('administrator', 'ucitelj', 'ucenec') [not null]
  datum_registracije datetime [not null]
  datum_rojstva date
  status enum('aktiven', 'neaktiven') [default: 'aktiven']
}

Table predmeti {
  id int [pk, increment]
  ime varchar(45) [not null]
  koda varchar(10) [not null, unique]
  opis text
  status enum('aktiven', 'neaktiven') [default: 'aktiven']
}

Table ucitelji_predmeti {
  id int [pk, increment]
  id_ucitelja int [not null]
  id_predmeta int [not null]
}

Table ucenci_predmeti {
  id int [pk, increment]
  id_ucenca int [not null]
  id_predmeta int [not null]
  datum_vpisa date [not null]
  status enum('vpisano', 'opuščeno') [default: 'vpisano']
}

Table gradiva {
  id int [pk, increment]
  naslov varchar(100) [not null]
  vsebina text
  tip enum('dokument', 'video', 'povezava', 'drugi') [not null]
  pot_do_datoteke varchar(255)
  id_predmeta int [not null]
  id_avtorja int [not null]  
  datum_objave datetime [not null]
  datum_spremembe datetime
  status enum('aktiven', 'arhiviran') [default: 'aktiven']
}

Table naloge {
  id int [pk, increment]
  naslov varchar(100) [not null]
  navodila text [not null]
  rok_oddaje datetime [not null]
  maksimalna_ocena int [default: 10]
  id_predmeta int [not null]
  id_avtorja int [not null]
  datum_objave datetime [not null]
  datum_spremembe datetime
  status enum('aktiven', 'zakljucen', 'arhiviran') [default: 'aktiven']
}

Table oddaje {
  id int [pk, increment]
  id_naloge int [not null]
  id_ucenca int [not null]
  datum_oddaje datetime [not null]
  ocena int
  komentar text
  pot_do_datoteke varchar(255) [not null]
  originalno_ime_datoteke varchar(255) [not null]
  status enum('oddano', 'v_ocenjevanju', 'ocenjeno', 'popravljanje') [default: 'oddano']
  datum_ocenjevanja datetime
}

// Reference between tables
Ref: ucitelji_predmeti.id_ucitelja > uporabniki.id
Ref: ucitelji_predmeti.id_predmeta > predmeti.id
Ref: ucenci_predmeti.id_ucenca > uporabniki.id
Ref: ucenci_predmeti.id_predmeta > predmeti.id
Ref: gradiva.id_predmeta > predmeti.id
Ref: gradiva.id_avtorja > uporabniki.id
Ref: naloge.id_predmeta > predmeti.id
Ref: naloge.id_avtorja > uporabniki.id
Ref: oddaje.id_naloge > naloge.id
Ref: oddaje.id_ucenca > uporabniki.id

