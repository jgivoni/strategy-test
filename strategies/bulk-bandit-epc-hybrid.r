
# Bulk best hybrid (beta+gamma) bandit on epc script

library(bandit)
experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)
	conversions <- c(<?= implode(',', $subtest['conversions']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)
        sumSqRev <- c(<?= implode(',', $subtest['sumSqRev']) ?>)
	subtest <- list(visits = visits, conversions = conversions, revenue = revenue, sumSqRev = sumSqRev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

bandit <- function(visits, conversions, revenue, sumSqRev) {
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
    #priorSumSqRev <- priorRevenue^2/5

    # adjust for priors
    r <- max(1 - visits / priorVisits, 0) # The ratio of primed observations to use (0-1)
    visits <- visits + priorVisits * r
    conversions <- conversions + priorConversions * r
    revenue <- revenue + priorRevenue * r
    sumSqRev <- sumSqRev + (priorRevenue * r)^2/5

    revPerConv <- revenue/conversions
    sdRpc <- sqrt((sumSqRev-revPerConv^2*conversions)/(conversions-1)^2) # standard deviation of revenue per signup

    arms <- length(visits)
    ndraws <- 2000 # Number of simulations

    alpha <- conversions + 1
    beta <- visits - conversions + 1
    b <- sdRpc^2/revPerConv
    k <- revPerConv/b
    weights <- as.vector(table(c(1:arms, sapply(split(
        rbeta(ndraws*arms, alpha, beta) *
        rgamma(ndraws*arms, shape = k, scale = b)
        , (0:(ndraws*arms-1) %/% arms)), function(x){return(which.max(x))})))/(ndraws+arms))

  subtest$weight <- weights
  return(weights)
}

weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$conversions, subtest$revenue, subtest$sumSqRev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
