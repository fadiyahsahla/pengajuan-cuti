<?php

namespace App\Helpers;

use App\Models\User;

class ApprovalHelper
{
    public static function getApprovalFlow($jabatanId)
    {
        $jabatanMapping = config('approval.jabatan_mapping');
        $flows = config('approval.flows');

        $jabatanSlug = $jabatanMapping[$jabatanId] ?? null;

        if (!$jabatanSlug) {
            return [];
        }

        return $flows[$jabatanSlug] ?? [];
    }

    public static function getApprovers($pengajuanCuti)
    {
        $flow = self::getApprovalFlow($pengajuanCuti->jabatan_id);
        $approvers = [];

        foreach ($flow as $level => $jabatanSlug) {
            $jabatanId = array_search($jabatanSlug, config('approval.jabatan_mapping'));

            $approver = User::where('jabatan_id', $jabatanId)
                ->where('divisi_id', $pengajuanCuti->divisi_id)
                ->where('is_active', true)
                ->first();

            if ($approver) {
                $approvers[] = [
                    'level' => $level,
                    'approver_id' => $approver->id,
                    'jabatan' => $jabatanSlug,
                ];
            }
        }

        return $approvers;
    }

    public static function canApprove($userId, $pengajuanCuti)
    {
        return $pengajuanCuti->approvals()
            ->where('approver_id', $userId)
            ->where('level_approval', $pengajuanCuti->current_level)
            ->where('status_approval', 'pending')
            ->exists();
    }

    public static function getNextLevel($currentLevel, $jabatanId)
    {
        $flow = self::getApprovalFlow($jabatanId);
        $levels = array_keys($flow);

        $currentIndex = array_search($currentLevel, $levels);

        if ($currentIndex === false || $currentIndex === count($levels) - 1) {
            return null;
        }

        return $levels[$currentIndex + 1];
    }
}
