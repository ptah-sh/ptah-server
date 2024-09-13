export interface ItemQuota {
  currentUsage: number;
  maxUsage: number;
  isSoftQuota: boolean;
  almostQuotaReached: boolean;
  isIntrinsic: boolean;
}

export interface UsageQuotas {
  nodes: ItemQuota;
  swarms: ItemQuota;
  services: ItemQuota;
  deployments: ItemQuota;
}