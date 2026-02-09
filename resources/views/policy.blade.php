<x-guest-layout>
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-authentication-card-logo />
            </div>

            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                <h1>Privacy Policy</h1>

                <p>Last updated: {{ date('F d, Y') }}</p>

                <p>This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>

                <h2>Interpretation and Definitions</h2>
                <h3>Interpretation</h3>
                <p>The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>

                <h3>Definitions</h3>
                <p>For the purposes of this Privacy Policy:</p>
                <ul>
                    <li><strong>Account</strong> means a unique account created for You to access our Service or parts of our Service.</li>
                    <li><strong>Company</strong> (referred to as either "the Company", "We", "Us" or "Our" in this Agreement) refers to {{ config('app.name') }}.</li>
                    <li><strong>Service</strong> refers to the Website.</li>
                    <li><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</li>
                </ul>

                <h2>Collecting and Using Your Personal Data</h2>
                <h3>Types of Data Collected</h3>
                <h4>Personal Data</h4>
                <p>While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:</p>
                <ul>
                    <li>Email address</li>
                    <li>First name and last name</li>
                    <li>Phone number</li>
                </ul>

                <h2>Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, You can contact us:</p>
                <ul>
                    <li>By visiting this page on our website: <a href="{{ route('contact') }}">{{ route('contact') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</x-guest-layout>
