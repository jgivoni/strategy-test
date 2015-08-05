
# Bulk best normal bandit on epc script

library(bandit)
suppressMessages(
    library(MCMCpack)
)

experiment = list()

<? foreach ($this->subtests as $subtest) : ?>
	visits <- c(<?= implode(',', $subtest['visits']) ?>)
	conversions <- c(<?= implode(',', $subtest['conversions']) ?>)
	revenue <- c(<?= implode(',', $subtest['revenue']) ?>)
        stdev <- c(<?= implode(',', $subtest['stdev']) ?>)
        sumSqRev <- c(<?= implode(',', $subtest['sumSqRev']) ?>)
        revPerConvStdev <- c(<?= implode(',', $subtest['revPerConvStdev']) ?>)
	subtest <- list(visits = visits, conversions = conversions, revenue = revenue, 
            sumSqRev = sumSqRev, stdev = stdev, revPerConvStdev = revPerConvStdev)
	experiment <- c(experiment, list(subtest))

<? endforeach; ?>

# Functions provided by Victor in "Continuous reward bandit.r"
sim_post_gaussian<-function (sum.revenues,sum.squared.revenues, n, mu0=0.5,nu0=100,alpha0 = 50, beta0 = 10000, ndraws = 5000) {
    k <- length(sum.revenues)
    ans <- matrix(nrow = ndraws, ncol = k)
    mu1<-(nu0*mu0+sum.revenues)/(nu0+n)
    nu1<-nu0+n
    alpha1<-alpha0+n/2
	beta1<-beta0+(sum.squared.revenues-((sum.revenues)**2)/n)/2+nu0*((sum.revenues-n*mu0)**2)/(2*(nu0+n))
	beta1[n==0]<-beta0
    for (i in (1:k)){
		sigma2<-rinvgamma(ndraws,alpha1[i],beta1[i])
		ans[, i] <- rnorm(ndraws, mu1[i],sqrt(sigma2/nu1[i]))
	}
    return(ans)
}

prob_winner_gaussian<-function (post){
    k = ncol(post)
    w = table(factor(max.col(post), levels = 1:k))
    return(w/sum(w))
}

best_gaussian_bandit_sim<-function (sum.revenues,sum.squared.revenues, n, mu0=0.5,nu0=100,alpha0 = 50, beta0 = 10000, ndraws = 5000) {
    return(prob_winner_gaussian(sim_post_gaussian(sum.revenues, sum.squared.revenues, n, mu0, nu0, alpha0, beta0, ndraws)))
}
#end functions

bandit <- function(visits, conversions, revenue, stdev, revPerConvStdev, sumSqRev) {
    weights <- best_gaussian_bandit_sim(revenue, sumSqRev, visits)
    subtest$weight <- weights
    return(weights)
}

weights = sapply(experiment, function(subtest) bandit(subtest$visits, subtest$conversions, subtest$revenue, 
    subtest$stdev, subtest$revPerConvStdev, subtest$sumSqRev)*10000)
write.table(t(weights), col.names = FALSE, row.names = FALSE)
