import Tool from './pages/Tool'
import Edit from './pages/Edit'
import Timeline from './pages/Timeline'
import Summary from './pages/Summary'
import PendingApproval from './pages/PendingApproval'
import ApprovedCovenant from './pages/ApprovedCovenant'

Nova.booting((app, store) => {
  Nova.inertia('Covenants', Tool)
  Nova.inertia('Edit', Edit)
  Nova.inertia('Timeline', Timeline)
  Nova.inertia('Summary', Summary)
  Nova.inertia('PendingApproval', PendingApproval)
  Nova.inertia('ApprovedCovenant', ApprovedCovenant)
})
