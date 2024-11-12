export interface ItemQuota {
  name: string;
  currentUsage: number;
  maxUsage: number;
  isSoftQuota: boolean;
  almostQuotaReached: boolean;
  isIntrinsic: boolean;
  resetPeriod: string;
}

export interface UsageQuotas {
  nodes: ItemQuota;
  swarms: ItemQuota;
  services: ItemQuota;
  reviewApps: ItemQuota;
  deployments: ItemQuota;
}