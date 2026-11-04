@extends('errors.layout')

@section('title', 'Trop de demandes')
@section('code', '429')
@section('message', 'Beaucoup de demandes sont arrivées d’un coup depuis cette connexion. Attends une minute, puis réessaie.')
