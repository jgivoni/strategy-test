
# Bulk best hybrid (beta+gamma) bandit on epc script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)
	conversions <- c(<?= implode(',', $subtest['conversions']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)
        stdev <- c(<?= implode(',', $subtest['stdev']) ?>)
        revPerConvStdev <- c(<?= implode(',', $subtest['revPerConvStdev']) ?>)
	subtest <- list(visits = visits, conversions = conversions, revenue = revenue, stdev = stdev, 
            revPerConvStdev = revPerConvStdev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bandit <- function(visits, conversions, revenue, stdev, revPerConvStdev) {
  # estimate the priors
  priorConversions <- 30 # Number of conversions per arm to estimate prior
  sumConversions <- sum(conversions)
  priorRatio <- ifelse(sumConversions > priorConversions, priorConversions / sumConversions, 1) # Ratio to adjust estimated prior
  priorVisits <- sum(visits) * priorRatio
  priorRevenue <- sum(revenue) * priorRatio
  priorConversions <- sumConversions * priorRatio

  # Hardcoded to improve results
  priorVisits <- 1000
  priorRevenue <- 750
  priorConversions <- 30

  # adjust for priors
  visits <- visits + priorVisits
  conversions <- conversions + priorConversions
  revenue <- revenue + priorRevenue

  revPerConv <- revenue/conversions
  #epc <- revenue/visits
    arms <- length(visits)
    
    pooled_sd <- sqrt(sum(revPerConvStdev^2*(conversions-1))/(sum(conversions)-arms))
    sdMean <- sqrt(pooled_sd^2/(conversions-1))

    ndraws <- 5000 # Number of simulations

    alpha <- conversions + 1
    beta <- visits - conversions + 1
    b <- sdMean^2/revPerConv
    k <- revPerConv/b
    weights <- as.vector(table(c(1:arms, sapply(split(
        rbeta(ndraws*arms, alpha, beta) *
        rgamma(ndraws*arms, shape = k, scale = b)
        , (0:(ndraws*arms-1) %/% arms)), function(x){return(which.max(x))})))/(ndraws+arms))

  subtest$weight <- weights
  return(weights)
}

weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$conversions, subtest$revenue, 
    subtest$stdev, subtest$revPerConvStdev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
