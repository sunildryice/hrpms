<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = new Status();
        $status->updateOrCreate([
            'id' => 1], [
            'title' => 'created',
            'status_class' => 'created badge bg-primary',
        ]);
        $status->updateOrCreate([
            'id' => 2], [
            'title' => 'returned',
            'status_class' => 'returned badge bg-warning',
        ]);
        $status->updateOrCreate([
            'id' => 3], [
            'title' => 'submitted',
            'status_class' => 'submitted badge bg-secondary',
        ]);
        $status->updateOrCreate([
            'id' => 4], [
            'title' => 'recommended',
            'status_class' => 'recommended badge bg-info',
        ]);
        $status->updateOrCreate([
            'id' => 5], [
            'title' => 'recommended',
            'status_class' => 'recommended2 badge bg-info',
        ]);
        $status->updateOrCreate([
            'id' => 6], [
            'title' => 'approved',
            'status_class' => 'approved badge bg-success',
        ]);
        $status->updateOrCreate([
            'id' => 7], [
            'title' => 'closed',
            'status_class' => 'closed badge bg-dark',
        ]);
        $status->updateOrCreate([
            'id' => 8], [
            'title' => 'rejected',
            'status_class' => 'rejected badge bg-danger',
        ]);
        $status->updateOrCreate([
            'id' => 9], [
            'title' => 'amended',
            'status_class' => 'amended badge bg-dark',
        ]);
        $status->updateOrCreate([
            'id' => 10], [
            'title' => 'assigned',
            'status_class' => 'assigned badge bg-dark',
        ]);
        $status->updateOrCreate([
            'id' => 11], [
            'title' => 'verified',
            'status_class' => 'verified badge bg-info',
        ]);
        $status->updateOrCreate([
            'id' => 12], [
            'title' => 'received',
            'status_class' => 'received badge bg-success',
        ]);
        $status->updateOrCreate([
            'id' => 13], [
            'title' => 'cancelled',
            'status_class' => 'cancelled badge bg-dark',
        ]);
        $status->updateOrCreate([
            'id' => 14], [
            'title' => 'verified',
            'status_class' => 'verified2 badge bg-info',
        ]);
        $status->updateOrCreate([
            'id' => 15], [
            'title' => 'send',
            'status_class' => 'send badge bg-info',
        ]);
        $status->updateOrCreate([
            'id' => 16], [
            'title' => 'paid',
            'status_class' => 'paid badge bg-success',
        ]);
        $status->updateOrCreate([
            'id' => 17], [
            'title' => 'distributed',
            'status_class' => 'distributed badge bg-warning',
        ]);
        $status->updateOrCreate([
            'id' => 18], [
            'title' => 'Cancel Submitted',
            'status_class' => 'submitted badge bg-danger',
        ]);
        $status->updateOrCreate([
            'id' => 19], [
            'title' => 'Verified',
            'status_class' => 'verified badge bg-info',
        ]);
    }
}
